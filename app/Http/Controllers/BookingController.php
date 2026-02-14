<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Vehicle;
use App\Services\KtpOcrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
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
    // Validasi Input (NIK tidak wajib diisi—diambil otomatis dari OCR foto KTP)
    $request->validate([
        'vehicle_id' => 'required|exists:vehicles,id',
        'customer_name' => 'required|string|max:255',
        'identity_card' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
    ]);

    $vehicle = Vehicle::findOrFail($request->vehicle_id);

    $fileName = null;
    $identityNumber = null;

    if ($request->hasFile('identity_card')) {
        $file = $request->file('identity_card');
        $fileName = time() . '_' . $file->getClientOriginalName();
        // OCR jalan dulu dari file upload (path pasti ada), baru simpan
        $identityNumber = KtpOcrService::extractNik($file->getRealPath());
        $file->storeAs('ktp', $fileName, 'public');
    }

    $days = Carbon::parse($request->start_date)
        ->diffInDays(Carbon::parse($request->end_date)) + 1;

    $totalCost = $days * $vehicle->price_per_day;

    Booking::create([
        'vehicle_id'      => $vehicle->id,
        'customer_name'   => $request->customer_name,
        'identity_number' => $identityNumber, // NIK dari OCR, atau null
        'identity_card'   => $fileName,
        'start_date'      => $request->start_date,
        'end_date'        => $request->end_date,
        'total_cost'      => $totalCost,
        'payment_status'  => 'pending'
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

        // Embed foto KTP sebagai Base64 agar DomPDF bisa menampilkan
        $ktpDataUri = null;
        if ($booking->identity_card) {
            $name = $booking->identity_card;
            $content = null;
            $mime = 'image/jpeg';

            // 1) Baca lewat Storage disk 'public' (path: ktp/namafile)
            if (Storage::disk('public')->exists('ktp/' . $name)) {
                $content = Storage::disk('public')->get('ktp/' . $name);
                $mime = Storage::disk('public')->mimeType('ktp/' . $name) ?: $mime;
            }
            // 2) Fallback: path lama storeAs('public/ktp', ...) → storage/app/private/public/ktp/
            if (!$content) {
                $path = storage_path('app/private/public/ktp/' . $name);
                if (file_exists($path)) {
                    $content = file_get_contents($path);
                    $mime = mime_content_type($path) ?: $mime;
                }
            }
            // 3) Fallback: storage/app/public/ktp/ dan public/storage/ktp/
            if (!$content) {
                foreach ([storage_path('app/public/ktp/' . $name), public_path('storage/ktp/' . $name)] as $path) {
                    if (file_exists($path)) {
                        $content = file_get_contents($path);
                        $mime = mime_content_type($path) ?: $mime;
                        break;
                    }
                }
            }

            if ($content !== null) {
                $ktpDataUri = 'data:' . $mime . ';base64,' . base64_encode($content);
            }
        }

        $pdf = Pdf::loadView('booking.pdf', compact('booking', 'ktpDataUri'));

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