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
    // Menampilkan daftar pengembalian kendaraan dengan fitur pencarian
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Mengambil data pengembalian beserta relasi booking dan kendaraan
        $query = ReturnVehicle::with('booking.vehicle')->latest();

        // Filter pencarian berdasarkan nama pelanggan, ID booking, atau plat nomor
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

    // Menampilkan form pengembalian kendaraan
    public function create($vehicle_id)
    {
        $vehicle = Vehicle::findOrFail($vehicle_id);

        // Mencari booking aktif yang status pembayarannya masih pending
        $booking = Booking::where('vehicle_id', $vehicle_id)
                          ->where('payment_status', 'pending')
                          ->first();

        if (!$booking) {
            return redirect()->back()->with('error', 'Kendaraan ini tidak memiliki jadwal sewa aktif.');
        }

        return view('returns.create', compact('vehicle', 'booking'));
    }

    // Memproses penyimpanan data pengembalian dan perhitungan denda
    public function store(Request $request)
    {
        // Validasi input data pengembalian
        $request->validate([
            'booking_id'        => 'required|exists:bookings,id',
            'vehicle_condition' => 'required|string'
        ]);

        $booking = Booking::with('vehicle')->findOrFail($request->booking_id);
        
        // Menentukan tanggal selesai sewa dan tanggal kembali
        $tglSelesaiSewa = Carbon::parse($booking->end_date)->startOfDay();
        $tglKembaliAsli = Carbon::now()->startOfDay();

        $lateDays = 0;
        $latePenalty = 0;

        // Hitung denda keterlambatan (Rp 50.000 per hari)
        if ($tglKembaliAsli->gt($tglSelesaiSewa)) {
            $lateDays = abs($tglKembaliAsli->diffInDays($tglSelesaiSewa));
            $latePenalty = $lateDays * 50000;
        }

        // Hitung denda kerusakan berdasarkan kondisi dan tipe kendaraan
        $damagePenalty = 0;
        $condition = trim($request->vehicle_condition);
        $vehicleType = $booking->vehicle->type; 

        if ($condition === 'Rusak Ringan' || $condition === 'Rusak Berat') {
            
            $baseDamageFee = ($condition === 'Rusak Ringan') ? 150000 : 500000;
            $multiplier = 1;
            
            // Pengali denda berdasarkan tipe kendaraan
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

        // Menggunakan Database Transaction untuk keamanan data
        DB::transaction(function () use ($booking, $lateDays, $totalPenalty, $condition) {
            // 1. Simpan data pengembalian
            ReturnVehicle::create([
                'booking_id'        => $booking->id,
                'return_date'       => Carbon::now(),
                'late_days'         => $lateDays,
                'penalty'           => $totalPenalty,
                'vehicle_condition' => $condition
            ]);

            // 2. Ubah status kendaraan menjadi tersedia
            Vehicle::where('id', $booking->vehicle_id)->update([
                'status' => 'available'
            ]);

            // 3. Ubah status booking menjadi selesai
            $booking->update([
                'payment_status' => 'completed'
            ]);
        });

        return redirect()->route('returns.index')
            ->with('success', 'Berhasil! Terlambat: ' . $lateDays . ' Hari. Total Denda: Rp ' . number_format($totalPenalty, 0, ',', '.'));
    }
}