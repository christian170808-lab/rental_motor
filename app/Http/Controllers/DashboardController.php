<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Booking;
use App\Models\ReturnVehicle;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard dengan statistik utama
     */
    public function index()
    {
        // Hitung jumlah motor berdasarkan status
        $totalMotor    = Vehicle::count();
        $motorTersedia = Vehicle::where('status', 'available')->count();
        $motorDisewa   = Vehicle::where('status', 'rented')->count();

        // Hitung total pendapatan dari booking (paid + completed) + denda keterlambatan/kerusakan
        $pendapatanBooking = Booking::whereIn('payment_status', ['paid', 'completed'])->sum('total_cost');
        $pendapatanDenda   = ReturnVehicle::sum('penalty');
        $pendapatan        = $pendapatanBooking + $pendapatanDenda;

        // Ambil data penyewaan per bulan untuk chart bar
        $monthly = Booking::select(
                DB::raw('MONTH(start_date) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('month')
            ->pluck('total', 'month');

        // Siapkan array 12 bulan (Jan-Des), isi 0 jika tidak ada data
        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartData[] = $monthly[$i] ?? 0;
        }

        return view('dashboard', compact(
            'totalMotor',
            'motorTersedia',
            'motorDisewa',
            'pendapatan',
            'chartData'
        ));
    }
}