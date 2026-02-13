<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\ReturnVehicle;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReturnController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        // --- PERBAIKAN 1: Gunakan 'booking.vehicle', bukan 'booking.kendaraan' ---
        $query = ReturnVehicle::with('booking.vehicle')->latest();

        if ($search) {
            $query->whereHas('booking', function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            })->orWhereHas('booking.vehicle', function ($q) use ($search) { // --- PERBAIKAN 2 ---
                $q->where('plate_number', 'like', "%{$search}%");
            });
        }

        $returns = $query->get();

        return view('returns.index', compact('returns', 'search'));
    }

    public function create($vehicle_id)
    {
        $vehicle = Vehicle::findOrFail($vehicle_id);

        $booking = Booking::where('vehicle_id', $vehicle_id)
                          ->where('payment_status', 'pending')
                          ->first();

        if (!$booking) {
            return redirect()->back()->with('error', 'Kendaraan ini tidak memiliki jadwal sewa aktif.');
        }

        return view('returns.create', compact('vehicle', 'booking'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'vehicle_condition' => 'required|string'
        ]);

        // --- PERBAIKAN 3: Gunakan 'vehicle', bukan 'kendaraan' ---
        $booking = Booking::with('vehicle')->findOrFail($request->booking_id);
        
        $tglSelesaiSewa = Carbon::parse($booking->end_date)->startOfDay();
        $tglKembaliAsli = Carbon::now()->startOfDay();

        $lateDays = 0;
        $latePenalty = 0;

        // Hitung Keterlambatan
        if ($tglKembaliAsli->gt($tglSelesaiSewa)) {
            $lateDays = abs($tglKembaliAsli->diffInDays($tglSelesaiSewa));
            $latePenalty = $lateDays * 50000;
        }

        // Hitung Denda Kerusakan
        $damagePenalty = 0;
        $condition = trim($request->vehicle_condition);
        
        // --- PERBAIKAN 4: Gunakan 'vehicle', bukan 'kendaraan' ---
        $vehicleType = $booking->vehicle->type; 

        if ($condition === 'Rusak Ringan' || $condition === 'Rusak Berat') {
            
            $baseDamageFee = ($condition === 'Rusak Ringan') ? 150000 : 500000;
            
            $multiplier = 1;
            
            switch (strtolower($vehicleType)) {
                case 'motor sport':
                case 'sport':
                    $multiplier = 2.0;
                    break;
                case 'trail / adventure':
                case 'trail':
                case 'adventure':
                    $multiplier = 1.5;
                    break;
                case 'skuter matik':
                case 'matik':
                    $multiplier = 1.0;
                    break;
                default:
                    $multiplier = 1.0;
                    break;
            }
            
            $damagePenalty = $baseDamageFee * $multiplier;
        }

        $totalPenalty = $latePenalty + $damagePenalty;

        // Database Transaction
        DB::transaction(function () use ($booking, $lateDays, $totalPenalty, $condition) {
            ReturnVehicle::create([
                'booking_id' => $booking->id,
                'return_date' => Carbon::now(),
                'late_days'   => $lateDays,
                'penalty'     => $totalPenalty,
                'vehicle_condition' => $condition
            ]);

            Vehicle::where('id', $booking->vehicle_id)->update([
                'status' => 'available'
            ]);

            $booking->update([
                'payment_status' => 'completed'
            ]);
        });

        return redirect()->route('returns.index')->with('success', 'Berhasil! Terlambat: ' . $lateDays . ' Hari. Total Denda: Rp ' . number_format($totalPenalty, 0, ',', '.'));
    }
}