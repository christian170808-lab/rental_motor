<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class BookingController extends Controller
{
    // Menampilkan daftar kendaraan untuk dipilih
    public function index()
    {
        $vehicles = Vehicle::all();
        return view('booking.index', compact('vehicles'));
    }

    // Menampilkan form booking untuk kendaraan tertentu
    public function create($vehicle_id)
    {
        $vehicle = Vehicle::findOrFail($vehicle_id);

        // Validasi jika kendaraan sedang disewa
        if ($vehicle->status == 'rented') {
            return back()->with('error', 'Vehicle Not Available');
        }

        

        return view('booking.create', compact('vehicle'));
    }

    // Memproses penyimpanan data booking baru
    public function store(Request $request)
    {
        // Validasi input form booking
        $request->validate([
            'vehicle_id'    => 'required|exists:vehicles,id',
            'customer_name' => 'required|string|max:255',
            'identity_card' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Upload foto KTP
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
        ]);

        $vehicle = Vehicle::findOrFail($request->vehicle_id);
        $fileName = null;

        // Proses unggah file foto KTP
        if ($request->hasFile('identity_card')) {
            $file = $request->file('identity_card');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('ktp', $fileName, 'public');
        }

        // Hitung total hari sewa
        $days = Carbon::parse($request->start_date)
            ->diffInDays(Carbon::parse($request->end_date)) + 1;

        // Hitung total biaya
        $totalCost = $days * $vehicle->price_per_day;

        // Simpan data booking
        Booking::create([
            'vehicle_id'      => $vehicle->id,
            'customer_name'   => $request->customer_name,
            'identity_number' => null, // NIK tidak disimpan untuk privasi
            'identity_card'   => $fileName,
            'start_date'      => $request->start_date,
            'end_date'        => $request->end_date,
            'total_cost'      => $totalCost,
            'payment_status'  => 'pending' // Status awal
        ]);

        // Ubah status kendaraan menjadi sedang disewa
        $vehicle->update(['status' => 'rented']);

        return redirect()->route('booking.index')
            ->with('success', 'Booking successful! ' . $vehicle->name . ' has been booked.');
    }

    // Mengunduh laporan booking dalam bentuk PDF
    public function downloadPdf($id)
    {
        $booking = Booking::with('vehicle')->findOrFail($id);

        // Konversi gambar KTP ke Base64 agar bisa terbaca di PDF
        $ktpDataUri = null;
        if ($booking->identity_card) {
            $name = $booking->identity_card;
            
            if (Storage::disk('public')->exists('ktp/' . $name)) {
                $content = Storage::disk('public')->get('ktp/' . $name);
                $mime = Storage::disk('public')->mimeType('ktp/' . $name);
                $ktpDataUri = 'data:' . $mime . ';base64,' . base64_encode($content);
            }
        }

        // Generate PDF dari view
        $pdf = Pdf::loadView('booking.pdf', compact('booking', 'ktpDataUri'));

        return $pdf->download('Laporan-Booking-'.$booking->id.'.pdf');
    }

    // Mengunduh laporan booking berdasarkan rentang tanggal
    public function laporanPeriode(Request $request)
    {
        // Validasi input tanggal
        $request->validate([
            'tanggal_awal'  => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_awal',
        ]);

        // Ambil data booking dalam rentang waktu tertentu
        $booking = Booking::whereBetween('start_date', [
            $request->tanggal_awal, 
            $request->tanggal_akhir
        ])->get();

        // Generate PDF laporan
        $pdf = Pdf::loadView('booking.laporan', compact('booking'));

        return $pdf->download('Laporan-Periode.pdf');
    }
}