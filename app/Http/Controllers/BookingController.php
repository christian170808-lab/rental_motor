<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Vehicle;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * Menampilkan daftar semua kendaraan dengan fitur pencarian dan filter tipe
     */
    public function index(Request $request)
    {
        $query = Vehicle::with('bookings');

        // Filter berdasarkan nama atau nomor plat
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('plate_number', 'like', '%' . $request->search . '%');
            });
        }

        // Filter berdasarkan tipe kendaraan (scooter, sport, adventure)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $vehicles = $query->orderByRaw("FIELD(type, 'scooter', 'trail', 'sport')")->get();

        return view('booking.index', compact('vehicles'));
    }

    /**
     * Menampilkan form booking untuk kendaraan tertentu
     * - Cek status kendaraan (tidak bisa booking jika sedang disewa)
     * - Load daftar customer untuk dropdown
     */
    public function create($vehicle_id)
    {
        $vehicle = Vehicle::findOrFail($vehicle_id);

        // Cegah booking jika motor sedang disewa
        if ($vehicle->status == 'rented') {
            return back()->with('error', 'Vehicle Not Available');
        }

        $customers = Customer::all(); // Untuk dropdown pilih customer

        return view('booking.create', compact('vehicle', 'customers'));
    }

    /**
     * Menyimpan data booking baru
     * - Validasi semua input
     * - Upload foto KTP dan bukti pembayaran
     * - Hitung total biaya berdasarkan jumlah hari
     * - Simpan booking dan ubah status motor jadi 'rented'
     *
     * PENTING: Nilai dari $request diambil SEBELUM closure DB::transaction
     * karena PHP closure tidak bisa mengakses $request secara langsung
     */
    public function store(Request $request)
    {
        // Validasi semua input form booking
        $request->validate([
            'vehicle_id'    => ['required', 'exists:vehicles,id'],
            'customer_id'   => ['required', 'exists:customers,id'],
            'identity_card' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'payment_proof' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'start_date'    => ['required', 'date'],
            'end_date'      => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $vehicle = Vehicle::findOrFail($request->vehicle_id);

        // Ambil nilai dari request SEBELUM masuk closure agar bisa diakses di dalam transaction
        $customerId = $request->customer_id;
        $startDate  = $request->start_date;
        $endDate    = $request->end_date;

        // Upload foto KTP ke storage/ktp/
        $ktpName = null;
        if ($request->hasFile('identity_card')) {
            $file    = $request->file('identity_card');
            $ktpName = time() . '_ktp_' . $file->getClientOriginalName();
            $file->storeAs('ktp', $ktpName, 'public');
        }

        // Upload bukti pembayaran ke storage/payments/
        $proofName = null;
        if ($request->hasFile('payment_proof')) {
            $file      = $request->file('payment_proof');
            $proofName = time() . '_proof_' . $file->getClientOriginalName();
            $file->storeAs('payments', $proofName, 'public');
        }

        // Hitung total hari dan biaya (+1 agar hari pertama terhitung)
        $days      = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
        $totalCost = $days * $vehicle->price_per_day;

        // Simpan booking dan ubah status motor dalam satu transaction
        // Jika salah satu gagal, semua rollback otomatis
        DB::transaction(function () use ($vehicle, $ktpName, $proofName, $totalCost, $customerId, $startDate, $endDate) {
            Booking::create([
                'vehicle_id'     => $vehicle->id,
                'customer_id'    => $customerId,
                'identity_card'  => $ktpName,
                'payment_proof'  => $proofName,
                'start_date'     => $startDate,
                'end_date'       => $endDate,
                'total_cost'     => $totalCost,
                'payment_status' => 'paid',
            ]);

            // Ubah status motor jadi 'rented' setelah booking berhasil
            $vehicle->update(['status' => 'rented']);
        });

        return redirect()->route('booking.index')->with('success', 'Booking successful and payment has been recorded!');
    }

    /**
     * Generate dan download PDF laporan booking
     * - Load foto KTP sebagai base64 agar bisa ditampilkan di PDF
     */
    public function downloadPdf($id)
    {
        $booking = Booking::with(['vehicle', 'customer'])->findOrFail($id);

        // Pastikan status pembayaran sudah 'paid'
        if ($booking->payment_status !== 'paid') {
            $booking->update(['payment_status' => 'paid']);
        }

        // Konversi foto KTP ke base64 agar bisa di-embed ke PDF
        $ktpDataUri = null;
        if ($booking->identity_card) {
            $path = 'ktp/' . $booking->identity_card;
            if (Storage::disk('public')->exists($path)) {
                $content    = Storage::disk('public')->get($path);
                $mime       = Storage::disk('public')->mimeType($path);
                $ktpDataUri = 'data:' . $mime . ';base64,' . base64_encode($content);
            }
        }

        $pdf = Pdf::loadView('booking.pdf', compact('booking', 'ktpDataUri'));

        return $pdf->download('Laporan-Booking-' . $booking->id . '.pdf');
    }

    /**
     * Mengembalikan motor dari booking tertentu (tanpa proses denda)
     * Untuk proses lengkap dengan denda, gunakan ReturnController
     */
    public function returnVehicle($id)
    {
        $booking = Booking::findOrFail($id);

        DB::transaction(function () use ($booking) {
            $vehicle = Vehicle::find($booking->vehicle_id);
            if ($vehicle) {
                $vehicle->update(['status' => 'available']); // Motor kembali tersedia
            }
        });

        return back()->with('success', 'The motorcycle has been successfully returned, and the revenue has been recorded!');
    }
}