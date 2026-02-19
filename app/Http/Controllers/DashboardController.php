<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Booking;
use App\Models\ReturnVehicle;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalMotor    = Vehicle::count();
        $motorTersedia = Vehicle::where('status', 'available')->count();
        $motorDisewa   = Vehicle::where('status', 'rented')->count();

        $pendapatanBooking = Booking::whereIn('payment_status', ['paid', 'completed'])->sum('total_cost');
        $pendapatanDenda   = ReturnVehicle::sum('penalty');
        $pendapatan        = $pendapatanBooking + $pendapatanDenda;

        $monthly = Booking::select(
                DB::raw('MONTH(start_date) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('month')
            ->pluck('total', 'month');

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