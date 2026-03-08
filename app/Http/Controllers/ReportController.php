<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    | - Filter by: date range (date_from, date_to), report type (type_filter)
    |
    | type_filter values:
    |   all       → semua data
    |   completed → payment_status = completed
    |   dp        → payment_status = paid AND payment_type = dp  (Incomplete/DP)
    |   full      → payment_status = paid AND payment_type = full (Paid Off)
    |   cancelled → payment_status = cancelled
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $sort        = $request->get('sort', 'newest');
        $dateFrom    = $request->get('date_from');
        $dateTo      = $request->get('date_to');
        $typeFilter  = $request->get('type_filter', 'all');

        // ── Bookings query ──
        $bookingQuery = Booking::with(['customer', 'vehicle', 'returnVehicle']);

        if ($dateFrom) {
            $bookingQuery->whereDate('start_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $bookingQuery->whereDate('start_date', '<=', $dateTo);
        }

        // Filter tipe laporan
        if ($typeFilter === 'completed') {
            $bookingQuery->where('payment_status', 'completed');
        } elseif ($typeFilter === 'dp') {
            $bookingQuery->where('payment_status', 'paid')
                         ->where('payment_type', 'dp');
        } elseif ($typeFilter === 'full') {
            $bookingQuery->where('payment_status', 'paid')
                         ->where('payment_type', 'full');
        } elseif ($typeFilter === 'cancelled') {
            $bookingQuery->where('payment_status', 'cancelled');
        }
        // 'all' = tidak ada filter tambahan

        if ($sort === 'oldest' || $sort === 'id_asc') {
            $bookingQuery->orderBy('id', 'asc');
        } else {
            $bookingQuery->latest();
        }

        $bookings = $bookingQuery->paginate(10)->withQueryString();

        // ── Payments query ──
        $paymentQuery = Payment::with(['customer', 'vehicle']);

        if ($dateFrom) {
            $paymentQuery->whereDate('start_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $paymentQuery->whereDate('start_date', '<=', $dateTo);
        }

        // Payments tidak punya payment_type, filter by status saja
        if ($typeFilter === 'completed') {
            $paymentQuery->where('status', 'completed');
        } elseif ($typeFilter === 'dp' || $typeFilter === 'full') {
            $paymentQuery->where('status', 'paid');
        }

        $payments = $paymentQuery->latest()->get();

        // ── Stats (ikut filter periode, exclude cancelled) ──
        $revenueQuery     = Payment::where('status', 'completed');
        $transactionQuery = Payment::whereIn('status', ['paid', 'completed']);
        $totalPaidQuery   = Payment::whereIn('status', ['paid', 'completed']);
        $totalAllQuery    = Booking::where('payment_status', '!=', 'cancelled');

        if ($dateFrom) {
            $revenueQuery->whereDate('start_date', '>=', $dateFrom);
            $transactionQuery->whereDate('start_date', '>=', $dateFrom);
            $totalPaidQuery->whereDate('start_date', '>=', $dateFrom);
            $totalAllQuery->whereDate('start_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $revenueQuery->whereDate('start_date', '<=', $dateTo);
            $transactionQuery->whereDate('start_date', '<=', $dateTo);
            $totalPaidQuery->whereDate('start_date', '<=', $dateTo);
            $totalAllQuery->whereDate('start_date', '<=', $dateTo);
        }

        $totalRevenue      = $revenueQuery->sum('total_price');
        $totalTransactions = $transactionQuery->count();
        $totalPaid         = $totalPaidQuery->sum('total_price');
        $totalAll          = $totalAllQuery->sum('total_cost');

        return view('reports.index', compact(
            'bookings',
            'payments',
            'totalRevenue',
            'totalTransactions',
            'totalPaid',
            'totalAll'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | DOWNLOAD ALL PDF
    |--------------------------------------------------------------------------
    */
    public function downloadPdf()
    {
        $bookings = $this->prepareImages($this->getAllBookings());
        return $this->generatePdf($bookings, 'Rental-Report-' . now()->format('d-m-Y') . '.pdf');
    }

    /*
    |--------------------------------------------------------------------------
    | DOWNLOAD SINGLE PDF
    |--------------------------------------------------------------------------
    */
    public function downloadSinglePdf($id)
    {
        $booking  = Booking::with(['customer', 'vehicle', 'returnVehicle'])->findOrFail($id);
        $bookings = $this->prepareImages(collect([$booking]));
        return $this->generatePdf($bookings, 'Booking-' . $booking->id . '-' . now()->format('d-m-Y') . '.pdf');
    }

    /*
    |--------------------------------------------------------------------------
    | PRIVATE HELPERS
    |--------------------------------------------------------------------------
    */
    private function getAllBookings()
    {
        return Booking::with(['customer', 'vehicle', 'returnVehicle'])->latest()->get();
    }

    private function prepareImages($bookings)
    {
        foreach ($bookings as $booking) {
            $booking->ktpDataUri   = $this->convertToBase64('ktp', $booking->identity_card);
            $booking->proofDataUri = $this->convertToBase64('payments', $booking->payment_proof);
        }
        return $bookings;
    }

    private function convertToBase64($folder, $fileName)
    {
        if (!$fileName) return null;
        $path = $folder . '/' . $fileName;
        if (!Storage::disk('public')->exists($path)) return null;
        $content = Storage::disk('public')->get($path);
        $mime    = Storage::disk('public')->mimeType($path);
        return 'data:' . $mime . ';base64,' . base64_encode($content);
    }

    private function generatePdf($bookings, $filename)
    {
        $pdf = Pdf::loadView('reports.pdf', compact('bookings'))
            ->setPaper('a4', 'portrait')
            ->setOption([
                'defaultFont'             => 'Arial',
                'isRemoteEnabled'         => true,
                'isHtml5ParserEnabled'    => true,
                'dpi'                     => 96,
                'defaultMediaType'        => 'print',
                'isFontSubsettingEnabled' => true,
            ]);
        return $pdf->download($filename);
    }
}