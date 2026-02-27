<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    | - Show report page with all bookings
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $bookings = $this->getBookings($request->get('sort', 'newest'));
        return view('reports.index', compact('bookings'));
    }

    /*
    |--------------------------------------------------------------------------
    | DOWNLOAD ALL PDF
    | - Generate PDF for all bookings with images as base64
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
    | - Generate PDF for one booking
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
    | PRIVATE: GET BOOKINGS
    |--------------------------------------------------------------------------
    */
    private function getBookings($sort = 'newest')
    {
        $query = Booking::with(['customer', 'vehicle', 'returnVehicle']);

        if ($sort === 'oldest' || $sort === 'id_asc') {
            $query->orderBy('id', 'asc');
        } else {
            $query->latest(); // newest (default)
        }

        return $query->paginate(10);
    }

    private function getAllBookings()
    {
        return Booking::with(['customer', 'vehicle', 'returnVehicle'])->latest()->get();
    }

    /*
    |--------------------------------------------------------------------------
    | PRIVATE: PREPARE IMAGES
    | - Convert stored images to base64 data URIs for PDF rendering
    |--------------------------------------------------------------------------
    */
    private function prepareImages($bookings)
    {
        foreach ($bookings as $booking) {
            $booking->ktpDataUri   = $this->convertToBase64('ktp', $booking->identity_card);
            $booking->proofDataUri = $this->convertToBase64('payments', $booking->payment_proof);
        }

        return $bookings;
    }

    /*
    |--------------------------------------------------------------------------
    | PRIVATE: CONVERT IMAGE TO BASE64
    |--------------------------------------------------------------------------
    */
    private function convertToBase64($folder, $fileName)
    {
        if (!$fileName) return null;

        $path = $folder . '/' . $fileName;

        if (!Storage::disk('public')->exists($path)) return null;

        $content = Storage::disk('public')->get($path);
        $mime    = Storage::disk('public')->mimeType($path);

        return 'data:' . $mime . ';base64,' . base64_encode($content);
    }

    /*
    |--------------------------------------------------------------------------
    | PRIVATE: GENERATE PDF
    |--------------------------------------------------------------------------
    */
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