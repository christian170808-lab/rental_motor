<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\ReturnVehicle;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReturnController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = ReturnVehicle::with('booking.vehicle')->latest();

        if ($search) {
            $query->whereHas('booking', function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            })->orWhereHas('booking.vehicle', function ($q) use ($search) {
                $q->where('plate_number', 'like', "%{$search}%");
            });
        }

        $returns = $query->get();

        return view('returns.index', compact('returns', 'search'));
    }

    public function create($vehicle_id)
    {
        $vehicle = Vehicle::findOrFail($vehicle_id);

        if ($vehicle->status === 'available') {
    return redirect()->route('booking.index')->with('error', 'Motor sedang tidak disewa.');
        }

        $booking = Booking::where('vehicle_id', $vehicle_id)
            ->whereDoesntHave('returnVehicle')
            ->latest()
            ->first();

        if (!$booking) {
            return back()->with('error', 'Active booking not found.');
        }

        return view('returns.create', compact('vehicle', 'booking'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'booking_id'        => ['required', 'exists:bookings,id'],
            'vehicle_condition' => ['required', 'string']
        ]);

        $booking = Booking::with('vehicle')->findOrFail($request->booking_id);

        $tglSelesaiSewa = Carbon::parse($booking->end_date)->startOfDay();
        $tglKembali = Carbon::now()->startOfDay();

        $lateDays = 0;
        $latePenalty = 0;

        if ($tglKembali->gt($tglSelesaiSewa)) {
            $lateDays = $tglKembali->diffInDays($tglSelesaiSewa);
            $latePenalty = $lateDays * 50000;
        }

        $damagePenalty = 0;
        $condition = trim($request->vehicle_condition);

        if ($condition === 'Minor Damage' || $condition === 'Major Damage') {
            $baseDamageFee = $condition === 'Minor Damage' ? 150000 : 500000;
            $multiplier = 1;

            switch (strtolower($booking->vehicle->type)) {
                case 'sport':
                    $multiplier = 2;
                    break;
                case 'trail':
                    $multiplier = 1.5;
                    break;
                case 'scooter':
                    $multiplier = 1;
                    break;
            }

            $damagePenalty = $baseDamageFee * $multiplier;
        }

        $totalPenalty = $latePenalty + $damagePenalty;

        try {
            DB::transaction(function () use ($booking, $lateDays, $totalPenalty, $condition) {
                ReturnVehicle::create([
                    'booking_id'        => $booking->id,
                    'return_date'       => Carbon::now(),
                    'late_days'         => $lateDays,
                    'penalty'           => $totalPenalty,
                    'vehicle_condition' => $condition
                ]);

                $booking->vehicle->update([
                    'status' => 'available'
                ]);

                $booking->update([
                    'payment_status' => 'completed'
                ]);
            });

            return redirect()->route('vehicles.index')->with('success', 
    'Success! Late: ' . $lateDays . ' days. Total Penalty: Rp ' . number_format($totalPenalty, 0, ',', '.')
            );

        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return back()->with('error', 'An error occurred while processing the return.');
        }
    }
}