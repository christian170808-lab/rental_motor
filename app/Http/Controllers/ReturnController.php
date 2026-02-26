<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\ReturnVehicle;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ReturnController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    | - List return history with optional search by booking ID or plate
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = ReturnVehicle::with('booking.vehicle')->latest();

        if ($search) {
            $query->whereHas('booking', function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%");
            })->orWhereHas('booking.vehicle', function ($q) use ($search) {
                $q->where('plate_number', 'like', "%{$search}%");
            });
        }

        $returns = $query->get();

        return view('returns.index', compact('returns', 'search'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    | - Show return form for a specific vehicle
    |--------------------------------------------------------------------------
    */
    public function create($vehicle_id)
    {
        $vehicle = Vehicle::findOrFail($vehicle_id);

        if ($vehicle->status === 'available') {
            return redirect()->route('booking.index')
                ->with('error', 'This motorcycle is not currently rented.');
        }

        $booking = Booking::where('vehicle_id', $vehicle_id)
            ->whereDoesntHave('returnVehicle')
            ->latest()
            ->first();

        if (!$booking) {
            return back()->with('error', 'No active booking found for this vehicle.');
        }

        return view('returns.create', compact('vehicle', 'booking'));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    | - Process vehicle return
    | - Calculate late penalty (Rp 50.000/day)
    | - Calculate damage penalty based on vehicle type
    |   Scooter: base fee | Sport: x2 | Trail: x1.5
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'booking_id'        => ['required', 'exists:bookings,id'],
            'vehicle_condition' => ['required', 'string'],
        ]);

        $booking = Booking::with('vehicle')->findOrFail($request->booking_id);

        // Calculate late penalty
        $endDate    = Carbon::parse($booking->end_date)->startOfDay();
        $returnDate = Carbon::now()->startOfDay();
        $lateDays   = 0;
        $latePenalty = 0;

        if ($returnDate->gt($endDate)) {
            $lateDays    = $endDate->diffInDays($returnDate);
            $latePenalty = $lateDays * 50000;
        }

        // Calculate damage penalty
        $condition     = trim($request->vehicle_condition);
        $damagePenalty = 0;

        if (in_array($condition, ['Minor Damage', 'Major Damage'])) {
            $baseFee = $condition === 'Minor Damage' ? 150000 : 500000;

            $multiplier = match (strtolower($booking->vehicle->type)) {
                'sport' => 2,
                'trail' => 1.5,
                default => 1, // scooter
            };

            $damagePenalty = $baseFee * $multiplier;
        }

        $totalPenalty = $latePenalty + $damagePenalty;

        try {
            DB::transaction(function () use ($booking, $lateDays, $totalPenalty, $condition) {
                ReturnVehicle::create([
                    'booking_id'        => $booking->id,
                    'return_date'       => Carbon::now(),
                    'late_days'         => $lateDays,
                    'penalty'           => $totalPenalty,
                    'vehicle_condition' => $condition,
                ]);

                $booking->vehicle->update(['status' => 'available']);
                $booking->update(['payment_status' => 'completed']);
            });

            return redirect()->route('vehicles.index')->with(
                'success',
                'Return processed! Late days: ' . $lateDays .
                '. Total fine: Rp ' . number_format($totalPenalty, 0, ',', '.')
            );

        } catch (\Exception $e) {
            Log::error('Return error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while processing the return.');
        }
    }
}