<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\ReturnVehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * ======================================
     * DASHBOARD SUMMARY
     * ======================================
     */
    public function index()
    {
        return view('dashboard', [
            'totalMotor'    => Vehicle::count(),
            'motorTersedia' => Vehicle::where('status', 'available')->count(),
            'motorDisewa'   => Vehicle::where('status', 'rented')->count(),
            'pendapatan'    => $this->calculateRevenue(),
            'rentedVehicles'=> $this->rentedVehicles(),
        ]);
    }

    /**
     * ======================================
     * CHART API
     * ======================================
     */
    public function chart(Request $request)
    {
        $type = $request->get('type', 'daily');

        return match ($type) {
            'daily'   => $this->dailyChart(),
            'weekly'  => $this->weeklyChart(),
            'monthly' => $this->monthlyChart(),
            'yearly'  => $this->yearlyChart(),
            'all'     => $this->allChart(),
            'custom'  => $this->customChart(
                $request->get('from'),
                $request->get('to')
            ),
            default   => response()->json([]),
        };
    }

    /**
     * ======================================
     * HELPER : TOTAL REVENUE
     * ======================================
     */
    private function calculateRevenue()
    {
        $payments = Payment::where('status', 'paid')->sum('total_price');
        $penalty  = ReturnVehicle::sum('penalty');

        return $payments + $penalty;
    }

    /**
     * ======================================
     * HELPER : RENTED VEHICLES
     * ======================================
     */
    private function rentedVehicles()
    {
        return Vehicle::where('status', 'rented')
            ->orderBy('name')
            ->paginate(5, ['*'], 'vehicle_page');
    }

    /**
     * ======================================
     * DAILY CHART  ← pakai created_at
     * ======================================
     */
    private function dailyChart()
    {
        $labels = [];
        for ($i = 6; $i >= 0; $i--) {
            $labels[] = now()->subDays($i)->format('d M');
        }

        $counts   = $this->bookingDaily();
        $revenues = $this->revenueDaily();

        $values = [];
        $revArr = [];
        for ($i = 6; $i >= 0; $i--) {
            $key      = now()->subDays($i)->format('Y-m-d');
            $values[] = (int)($counts[$key] ?? 0);
            $revArr[] = (float)($revenues[$key] ?? 0);
        }

        return response()->json([
            'labels'   => $labels,
            'values'   => $values,
            'revenues' => $revArr,
        ]);
    }

    /**
     * ======================================
     * WEEKLY CHART  ← 4 minggu terakhir
     * ======================================
     */
    private function weeklyChart()
    {
        $labels = [];
        $values = [];
        $revArr = [];

        for ($i = 3; $i >= 0; $i--) {
            $start = now()->startOfWeek()->subWeeks($i);
            $end   = (clone $start)->endOfWeek();

            $label    = $start->format('d M') . ' - ' . $end->format('d M');
            $labels[] = $label;

            $values[] = (int) Booking::whereBetween('created_at', [$start, $end])
                ->count();

            $revArr[] = (float) Payment::whereBetween('created_at', [$start, $end])
                ->where('status', 'paid')
                ->sum('total_price');
        }

        return response()->json([
            'labels'   => $labels,
            'values'   => $values,
            'revenues' => $revArr,
        ]);
    }


    private function monthlyChart()
    {
        $months = ['January','February','March','April','May','June',
                   'July','August','September','October','November','December'];

        // Gunakan created_at agar data yang baru dibuat langsung masuk
        $counts = Booking::whereYear('created_at', now()->year)
            ->select(DB::raw('MONTH(created_at) as month_key'), DB::raw('COUNT(*) as total'))
            ->groupBy('month_key')
            ->pluck('total', 'month_key');

        $revenues = Payment::whereYear('created_at', now()->year)
            ->where('status','paid')
            ->select(DB::raw('MONTH(created_at) as month_key'), DB::raw('SUM(total_price) as total'))
            ->groupBy('month_key')
            ->pluck('total', 'month_key');

        $values = [];
        $revArr = [];
        foreach ($months as $i => $month) {
            $monthNum = $i + 1; // January = 1, February = 2, dst
            $values[] = (int)($counts[$monthNum] ?? 0);
            $revArr[] = (float)($revenues[$monthNum] ?? 0);
        }

        return response()->json([
            'labels'   => $months,
            'values'   => $values,
            'revenues' => $revArr,
        ]);
    }

    /**
     * ======================================
     * YEARLY CHART  ← pakai created_at
     * ======================================
     */
    private function yearlyChart()
    {
        $startYear = Booking::min(DB::raw('YEAR(created_at)')) ?? now()->year;
        $endYear   = now()->year;

        $counts = Booking::select(
                DB::raw('YEAR(created_at) as year_key'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('year_key')
            ->pluck('total', 'year_key');

        $revenues = Payment::where('status','paid')
            ->select(
                DB::raw('YEAR(created_at) as year_key'),
                DB::raw('SUM(total_price) as total')
            )
            ->groupBy('year_key')
            ->pluck('total', 'year_key');

        $labels = range($startYear, $endYear);

        $values = [];
        $revArr = [];
        foreach ($labels as $year) {
            $values[] = (int)($counts[$year] ?? 0);
            $revArr[] = (float)($revenues[$year] ?? 0);
        }

        return response()->json([
            'labels'   => $labels,
            'values'   => $values,
            'revenues' => $revArr,
        ]);
    }

    /**
     * ======================================
     * ALL DATA CHART  ← pakai created_at
     * ======================================
     */
    private function allChart()
    {
        $data = Booking::select(
                DB::raw('DATE_FORMAT(created_at,"%b %Y") as label'),
                DB::raw('YEAR(created_at) as yr'),
                DB::raw('MONTH(created_at) as mo'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('label','yr','mo')
            ->orderBy('yr')
            ->orderBy('mo')
            ->get();

        $revData = Payment::where('status','paid')
            ->select(
                DB::raw('DATE_FORMAT(created_at,"%b %Y") as label'),
                DB::raw('SUM(total_price) as total')
            )
            ->groupBy('label')
            ->pluck('total','label');

        return response()->json([
            'labels'   => $data->pluck('label'),
            'values'   => $data->pluck('total'),
            'revenues' => $data->map(
                fn($d) => (float)($revData[$d->label] ?? 0)
            )->values(),
        ]);
    }

    /**
     * ======================================
     * CUSTOM RANGE CHART  ← pakai created_at
     * ======================================
     */
    private function customChart($from, $to)
    {
        $data = Booking::select(
                DB::raw('DATE(created_at) as label'),
                DB::raw('COUNT(*) as total')
            )
            ->whereBetween('created_at', [
                $from . ' 00:00:00',
                $to   . ' 23:59:59',
            ])
            ->groupBy('label')
            ->orderBy('label')
            ->get();

        $revData = Payment::where('status','paid')
            ->whereBetween('created_at', [
                $from . ' 00:00:00',
                $to   . ' 23:59:59',
            ])
            ->select(
                DB::raw('DATE(created_at) as label'),
                DB::raw('SUM(total_price) as total')
            )
            ->groupBy('label')
            ->pluck('total','label');

        return response()->json([
            'labels'   => $data->pluck('label'),
            'values'   => $data->pluck('total'),
            'revenues' => $data->map(
                fn($d) => (float)($revData[$d->label] ?? 0)
            )->values(),
        ]);
    }

    /**
     * ======================================
     * SMALL HELPERS  ← pakai created_at
     * ======================================
     */
    private function bookingDaily()
    {
        return Booking::whereBetween('created_at', [
                now()->subDays(6)->startOfDay(),
                now()->endOfDay(),
            ])
            ->select(DB::raw('DATE(created_at) as date_key'), DB::raw('COUNT(*) as total'))
            ->groupBy('date_key')
            ->pluck('total', 'date_key');
    }

    private function revenueDaily()
    {
        return Payment::whereBetween('created_at', [
                now()->subDays(6)->startOfDay(),
                now()->endOfDay(),
            ])
            ->where('status','paid')
            ->select(DB::raw('DATE(created_at) as date_key'), DB::raw('SUM(total_price) as total'))
            ->groupBy('date_key')
            ->pluck('total', 'date_key');
    }

    private function formatChart($labels, $counts, $revenues)
    {
        $values = [];
        $revArr = [];

        foreach ($labels as $label) {
            $values[] = $counts[$label] ?? 0;
            $revArr[] = (float)($revenues[$label] ?? 0);
        }

        return response()->json([
            'labels'   => $labels,
            'values'   => $values,
            'revenues' => $revArr,
        ]);
    }

    private function loopNumericChart($labels, $counts, $revenues)
    {
        $values = [];
        $revArr = [];

        foreach ($labels as $key) {
            $values[] = $counts[$key] ?? 0;
            $revArr[] = (float)($revenues[$key] ?? 0);
        }

        return response()->json([
            'labels'   => $labels,
            'values'   => $values,
            'revenues' => $revArr,
        ]);
    }
}