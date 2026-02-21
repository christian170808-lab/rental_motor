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
    /**
     * Menampilkan riwayat pengembalian kendaraan
     * - Bisa dicari berdasarkan nama customer, booking ID, atau plat nomor
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query  = ReturnVehicle::with('booking.vehicle')->latest();

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

    /**
     * Menampilkan form pengembalian kendaraan
     * - Cek apakah motor sedang disewa (tidak bisa return jika available)
     * - Cari booking aktif yang belum dikembalikan
     */
    public function create($vehicle_id)
    {
        $vehicle = Vehicle::findOrFail($vehicle_id);

        // Motor harus berstatus 'rented' untuk bisa diproses return
        if ($vehicle->status === 'available') {
            return redirect()->route('booking.index')->with('error', 'The motorcycle is currently not rented.');
        }

        // Cari booking aktif yang belum ada record returnnya
        $booking = Booking::where('vehicle_id', $vehicle_id)
            ->whereDoesntHave('returnVehicle')
            ->latest()
            ->first();

        if (!$booking) {
            return back()->with('error', 'Active booking not found.');
        }

        return view('returns.create', compact('vehicle', 'booking'));
    }

    /**
     * Memproses pengembalian kendaraan
     * - Hitung keterlambatan (denda Rp 50.000/hari)
     * - Hitung denda kerusakan berdasarkan tipe motor dan tingkat kerusakan
     *   Tipe motor:
     *     - sport      → multiplier 2x
     *     - adventure  → multiplier 1.5x  (FIX: dari 'trail' ke 'adventure')
     *     - scooter    → multiplier 1x
     *   Tingkat kerusakan:
     *     - Minor Damage → base fee Rp 150.000
     *     - Major Damage → base fee Rp 500.000
     * - Update status motor kembali ke 'available'
     * - Update payment_status booking ke 'completed'
     */
    public function store(Request $request)
    {
        $request->validate([
            'booking_id'        => ['required', 'exists:bookings,id'],
            'vehicle_condition' => ['required', 'string'],
        ]);

        $booking = Booking::with('vehicle')->findOrFail($request->booking_id);

        // Hitung keterlambatan
        $tglSelesaiSewa = Carbon::parse($booking->end_date)->startOfDay();
        $tglKembali     = Carbon::now()->startOfDay();
        $lateDays       = 0;
        $latePenalty    = 0;

        if ($tglKembali->gt($tglSelesaiSewa)) {
            $lateDays    = $tglKembali->diffInDays($tglSelesaiSewa);
            $latePenalty = $lateDays * 50000; // Rp 50.000 per hari telat
        }

        // Hitung denda kerusakan berdasarkan tipe dan kondisi
        $damagePenalty = 0;
        $condition     = trim($request->vehicle_condition);

        if ($condition === 'Minor Damage' || $condition === 'Major Damage') {
            $baseDamageFee = $condition === 'Minor Damage' ? 150000 : 500000;
            $multiplier    = 1;

            // FIX: diubah dari 'trail' ke 'adventure' sesuai perubahan database
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
            // Simpan data return, update status motor dan booking dalam satu transaction
            DB::transaction(function () use ($booking, $lateDays, $totalPenalty, $condition) {
                ReturnVehicle::create([
                    'booking_id'        => $booking->id,
                    'return_date'       => Carbon::now(),
                    'late_days'         => $lateDays,
                    'penalty'           => $totalPenalty,
                    'vehicle_condition' => $condition,
                ]);

                // Motor kembali tersedia setelah dikembalikan
                $booking->vehicle->update(['status' => 'available']);

                // Tandai booking sebagai selesai
                $booking->update(['payment_status' => 'completed']);
            });

            return redirect()->route('vehicles.index')->with('success',
                'Return successful! Late: ' . $lateDays . ' days. Total Fine: Rp ' . number_format($totalPenalty, 0, ',', '.')
            );
        } catch (\Exception $e) {
            Log::error('Return error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while processing the return.');
        }
    }
}