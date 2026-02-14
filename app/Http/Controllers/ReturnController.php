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

        // Mengambil data berdasarkan query yang sudah difilter
        $returns = $query->get();

        // Mengirim data ke view
        return view('returns.index', compact('returns', 'search'));
    }

    // Menampilkan form pengembalian kendaraan
    public function create($vehicle_id)
    {
        // Mencari data kendaraan berdasarkan ID
        $vehicle = Vehicle::findOrFail($vehicle_id);

        // Memastikan kendaraan sedang tersewa sebelum mengembalikan
        if ($vehicle->status === 'available') {
            return redirect()->back()->with('error', 'This vehicle is already available (not currently rented).');
        }

        // Mencari booking aktif yang status kendaraannya masih rented
        $booking = Booking::where('vehicle_id', $vehicle_id)
                          ->where('payment_status', 'pending')
                          ->latest()
                          ->first();

        // Validasi apakah data booking ditemukan
        if (!$booking) {
            return redirect()->back()->with('error', 'This vehicle is detected as rented, but the booking data was not found.');
        }

        // Mengirim data ke view form pengembalian
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

        // Mengambil data booking beserta relasi kendaraan
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

        // Logika perhitungan denda kerusakan
        if ($condition === 'Minor Damage' || $condition === 'Major Damage') {
            
            $baseDamageFee = ($condition === 'Minor Damage') ? 150000 : 500000;
            $multiplier = 1;
            
            // Pengali denda berdasarkan tipe kendaraan
            switch (strtolower($vehicleType)) {
                case 'motor sport':
                case 'sport':
                    $multiplier = 40.0;
                    break;
                case 'trail / adventure':
                case 'trail':
                case 'adventure':
                    $multiplier = 25.0;
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

        // Total keseluruhan denda
        $totalPenalty = $latePenalty + $damagePenalty;

        // Menggunakan Database Transaction untuk keamanan data
        try {
            DB::transaction(function () use ($booking, $lateDays, $totalPenalty, $condition) {
                // 1. Simpan data pengembalian ke tabel returns
                ReturnVehicle::create([
                    'booking_id'        => $booking->id,
                    'return_date'       => Carbon::now(),
                    'late_days'         => $lateDays,
                    'penalty'           => $totalPenalty,
                    'vehicle_condition' => $condition
                ]);

                // 2. Ubah status kendaraan menjadi tersedia
                $booking->vehicle->update([
                    'status' => 'available'
                ]);

                // 3. Ubah status booking menjadi selesai
                $booking->update([
                    'payment_status' => 'completed'
                ]);
            });

            // Redirect ke halaman daftar pengembalian dengan pesan sukses
            return redirect()->route('returns.index')
                ->with('success', 'Success! Late: ' . $lateDays . ' Days. Total Penalty: Rp ' . number_format($totalPenalty, 0, ',', '.'));
        
        } catch (\Exception $e) {
            // Log error jika terjadi kesalahan database
            Log::error('Error saat pengembalian kendaraan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while processing the return.');
        }
    }
}