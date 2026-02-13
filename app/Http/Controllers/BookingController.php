<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Carbon\Carbon;
// Library PDF - Sudah ada di use statement
use Barryvdh\DomPDF\Facade\Pdf;

class BookingController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::all();
        return view('booking.index', compact('vehicles'));
    }

    public function create($vehicle_id)
    {
        $vehicle = Vehicle::findOrFail($vehicle_id);

        if ($vehicle->status == 'rented') {
            return back()->with('error', 'Vehicle Not Available');
        }

        session()->flash('success', 'Silakan isi form berikut untuk menyewa ' . $vehicle->name);

        return view('booking.create', compact('vehicle'));
    }

    public function store(Request $request)
    {
        // Validasi Input
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'customer_name' => 'required|string|max:255',
            'identity_card' => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $vehicle = Vehicle::findOrFail($request->vehicle_id);

        // Logika Hitung Biaya
        $days = Carbon::parse($request->start_date)
            ->diffInDays(Carbon::parse($request->end_date)) + 1;

        $totalCost = $days * $vehicle->price_per_day;

        // Simpan data
        Booking::create([
            'vehicle_id'     => $vehicle->id,
            'customer_name'  => $request->customer_name,
            'identity_card'  => $request->identity_card,
            'start_date'     => $request->start_date,
            'end_date'       => $request->end_date,
            'total_cost'     => $totalCost,
            'payment_status' => 'pending'
        ]);

        // Update Status Kendaraan
        $vehicle->update([
            'status' => 'rented'
        ]);

        return redirect()->route('booking.index')
                         ->with('success', 'Booking berhasil! ' . $vehicle->name . ' telah dipesan.');
    }

    // --- PERBAIKAN: Relasi 'pelanggan' dihapus ---
    public function downloadPdf($id)
    {
        // Memuat relasi 'vehicle' saja
        $booking = Booking::with('vehicle')->findOrFail($id); 

        $pdf = Pdf::loadView('booking.pdf', compact('booking'));

        return $pdf->download('Laporan-Booking-'.$booking->id.'.pdf');
    }

    // --- FUNGSI BARU: Laporan berdasarkan periode ---
    public function laporanPeriode(Request $request)
    {
        // Validasi input tanggal
        $request->validate([
            'tanggal_awal' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_awal',
        ]);

        // Mengambil data berdasarkan rentang tanggal 'start_date'
        $booking = Booking::whereBetween('start_date', 
            [$request->tanggal_awal, $request->tanggal_akhir])
            ->get();

        $pdf = Pdf::loadView('booking.laporan', compact('booking'));

        return $pdf->download('Laporan-Periode.pdf');
    }
}