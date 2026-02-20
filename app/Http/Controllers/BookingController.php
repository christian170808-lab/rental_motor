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
    public function index(Request $request)
    {
        $query = Vehicle::with('bookings');

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('plate_number', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $vehicles = $query->get(); 

        return view('booking.index', compact('vehicles'));
    }

    public function create($vehicle_id)
    {
        $vehicle = Vehicle::findOrFail($vehicle_id);

        if ($vehicle->status == 'rented') {
            return back()->with('error', 'Vehicle Not Available');
        }
 $customers = \App\Models\Customer::all(); // ← tambah ini

    return view('booking.create', compact('vehicle', 'customers')); // ← tambah customers
    }

    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id'    => ['required', 'exists:vehicles,id'],
            'customer_id' => ['required', 'exists:customers,id'],
            'identity_card' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'payment_proof' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'start_date'    => ['required', 'date'],
            'end_date'      => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $vehicle = Vehicle::findOrFail($request->vehicle_id);

        $ktpName = null;
        if ($request->hasFile('identity_card')) {
            $file = $request->file('identity_card');
            $ktpName = time() . '_ktp_' . $file->getClientOriginalName();
            $file->storeAs('ktp', $ktpName, 'public');
        }

        $proofName = null;
        if ($request->hasFile('payment_proof')) {
            $file = $request->file('payment_proof');
            $proofName = time() . '_proof_' . $file->getClientOriginalName();
            $file->storeAs('payments', $proofName, 'public');
        }

        $days = Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date)) + 1;
        $totalCost = $days * $vehicle->price_per_day;

        DB::transaction(function () use ($request, $vehicle, $ktpName, $proofName, $totalCost) {
            Booking::create([
                'vehicle_id'      => $vehicle->id,
               'customer_id' => Customer::where('customer_id', $request->customer_id)->value('id'),
                'identity_number' => $request->identity_number,
                'identity_card'   => $ktpName,
                'payment_proof'   => $proofName,
                'start_date'      => $request->start_date,
                'end_date'        => $request->end_date,
                'total_cost'      => $totalCost,
                'payment_status'  => 'paid'
            ]);

            $vehicle->update(['status' => 'rented']);
        });

        return redirect()->route('booking.index')->with('success', 'Booking berhasil dan pembayaran tercatat!');
    }

    public function downloadPdf($id)
    {
        $booking = Booking::with(['vehicle', 'customer'])->findOrFail($id);

        if ($booking->payment_status !== 'paid') {
            $booking->update(['payment_status' => 'paid']);
        }

        $ktpDataUri = null;
        if ($booking->identity_card) {
            $path = 'ktp/' . $booking->identity_card;
            if (Storage::disk('public')->exists($path)) {
                $content = Storage::disk('public')->get($path);
                $mime = Storage::disk('public')->mimeType($path);
                $ktpDataUri = 'data:' . $mime . ';base64,' . base64_encode($content);
            }
        }

        $pdf = Pdf::loadView('booking.pdf', compact('booking', 'ktpDataUri'));

        return $pdf->download('Laporan-Booking-' . $booking->id . '.pdf');
    }

    public function returnVehicle($id)
    {
        $booking = Booking::findOrFail($id);

        DB::transaction(function () use ($booking) {
            $vehicle = Vehicle::find($booking->vehicle_id);
            if ($vehicle) {
                $vehicle->update(['status' => 'available']);
            }
        });

        return back()->with('success', 'Motor berhasil dikembalikan, pendapatan tetap tercatat!');
    }
}