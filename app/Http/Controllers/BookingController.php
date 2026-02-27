<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Vehicle;
use App\Models\Customer;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    | - List all rented vehicles
    | - Filter by search & type
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $query = Vehicle::with(['bookings.customer']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('plate_number', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->whereHas('bookings', function ($q) use ($request) {
                if ($request->status === 'dp') {
                    $q->where('payment_status', 'paid')->where('payment_type', 'dp');
                } elseif ($request->status === 'paid') {
                    $q->where('payment_status', 'paid')->where('payment_type', 'full');
                }
            });
        }

        $vehicles = $query->orderByRaw("FIELD(status, 'rented', 'available')")
                        ->orderByRaw("FIELD(type, 'scooter', 'sport', 'trail')")
                        ->orderBy('price_per_day', 'asc')
                        ->paginate(10)
                        ->withQueryString();

        $customers = Customer::all();

        $rentedVehiclesQuery = Vehicle::with(['bookings.customer']);

        if ($request->filled('search')) {
            $rentedVehiclesQuery->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('plate_number', 'like', '%' . $request->search . '%')
                ->orWhere('type', 'like', '%' . $request->search . '%')
                ->orWhereHas('bookings.customer', function ($cq) use ($request) {
                    $cq->where('customer_name', 'like', '%' . $request->search . '%')
                        ->orWhere('phone_number', 'like', '%' . $request->search . '%');
                });
            });
        }

        if ($request->filled('type')) {
            $rentedVehiclesQuery->where('type', $request->type);
        }

        $rentedVehicles = $rentedVehiclesQuery->whereHas('bookings', function ($q) use ($request) {
            if ($request->status === 'dp') {
                $q->where('payment_status', 'paid')->where('payment_type', 'dp');
            } elseif ($request->status === 'paid') {
                $q->where('payment_status', 'paid')->where('payment_type', 'full');
            } else {
                $q->where('payment_status', 'paid')->whereIn('payment_type', ['dp', 'full']);
            }
        })->paginate(10);

        return view('booking.index', compact('vehicles', 'customers', 'rentedVehicles'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    | - Show booking form for a specific vehicle
    |--------------------------------------------------------------------------
    */
    public function create($vehicle_id)
    {
        $vehicle = Vehicle::findOrFail($vehicle_id);

        if ($vehicle->status === 'rented') {
            return back()->with('error', 'Vehicle is not available.');
        }

        $customers = Customer::all();
        return view('booking.create', compact('vehicle', 'customers'));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    | - Validate, upload files, calculate cost, create booking & payment
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id'    => ['required', 'exists:vehicles,id'],
            'customer_id'   => ['required', 'exists:customers,id'],
            'identity_card' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'payment_proof' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'start_date'    => ['required', 'date'],
            'end_date'      => ['required', 'date', 'after_or_equal:start_date'],
            'payment_type'  => ['required', 'in:dp,full'],
        ]);

        $vehicle     = Vehicle::findOrFail($request->vehicle_id);
        $customerId  = $request->customer_id;
        $startDate   = $request->start_date;
        $endDate     = $request->end_date;
        $paymentType = $request->payment_type;

        // Upload identity card (KTP)
        $ktpName = null;
        if ($request->hasFile('identity_card')) {
            $file    = $request->file('identity_card');
            $ktpName = time() . '_ktp_' . $file->getClientOriginalName();
            $file->storeAs('ktp', $ktpName, 'public');
        }

        // Upload payment proof
        $proofName = null;
        if ($request->hasFile('payment_proof')) {
            $file      = $request->file('payment_proof');
            $proofName = time() . '_proof_' . $file->getClientOriginalName();
            $file->storeAs('payments', $proofName, 'public');
        }

        // Calculate cost
        $days       = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
        $totalCost  = $days * $vehicle->price_per_day;
        $amountPaid = $paymentType === 'full' ? $totalCost : $totalCost * 0.5;

        DB::transaction(function () use (
            $vehicle, $ktpName, $proofName, $totalCost, $amountPaid,
            $customerId, $startDate, $endDate, $days, $paymentType
        ) {
            $booking = Booking::create([
                'vehicle_id'     => $vehicle->id,
                'customer_id'    => $customerId,
                'identity_card'  => $ktpName,
                'payment_proof'  => $proofName,
                'start_date'     => $startDate,
                'end_date'       => $endDate,
                'total_cost'     => $totalCost,
                'payment_status' => 'paid',
                'payment_type'   => $paymentType,
            ]);

            Payment::create([
                'booking_id'  => $booking->id,
                'customer_id' => $customerId,
                'vehicle_id'  => $vehicle->id,
                'start_date'  => $startDate,
                'end_date'    => $endDate,
                'duration'    => $days,
                'total_price' => $amountPaid,
                'status'      => 'paid',
            ]);

            $vehicle->update(['status' => 'rented']);
        });

        return redirect()->route('booking.index')->with('success', 'Booking created successfully!');
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW
    | - Display booking detail with KTP & payment proof images
    |--------------------------------------------------------------------------
    */
    public function show($id)
    {
        $booking = Booking::with(['vehicle', 'customer', 'returnVehicle'])->findOrFail($id);

        if ($booking->payment_status !== 'paid') {
            $booking->update([
    'payment_type' => 'full',
]);
        }

        $ktpDataUri = $this->getImageDataUri('ktp', $booking->identity_card);
        $proofDataUri = $this->getImageDataUri('payments', $booking->payment_proof);

        return view('booking.show', compact('booking', 'ktpDataUri', 'proofDataUri'));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    | - Update booking dates and customer contact info
    | - Recalculate total cost based on new dates
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, $id)
    {
        $booking = Booking::with('customer')->findOrFail($id);

        $request->validate([
            'phone_number'  => 'required|string|max:20',
            'customer_name' => 'required|string|max:100',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
        ]);

        if ($booking->customer) {
            $booking->customer->update([
                'phone_number'  => $request->phone_number,
                'customer_name' => $request->customer_name,
            ]);
        }

        $vehicle   = Vehicle::findOrFail($booking->vehicle_id);
        $days      = Carbon::parse($request->start_date)->diffInDays($request->end_date) + 1;
        $totalCost = $days * $vehicle->price_per_day;

        $booking->update([
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'total_cost' => $totalCost,
        ]);

        return redirect()->route('booking.index')->with('success', 'Booking updated successfully!');
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    | - Load edit form for a vehicle
    |--------------------------------------------------------------------------
    */
    public function edit($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        return view('booking.edit', compact('vehicle'));
    }

    /*
    |--------------------------------------------------------------------------
    | RETURN VEHICLE (via Pelunasan flow)
    | - Mark booking as completed, set vehicle back to available
    |--------------------------------------------------------------------------
    */
    public function returnVehicle($id)
    {
        $booking = Booking::findOrFail($id);

        DB::transaction(function () use ($booking) {
    $booking->update([
        'payment_type' => 'full',
    ]);
});

        return redirect()->route('booking.index')->with('success', 'Vehicle returned successfully!');
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY
    | - Delete vehicle along with related bookings and returns
    |--------------------------------------------------------------------------
    */
    public function destroy($id)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $bookingIds = DB::table('bookings')
            ->where('vehicle_id', $id)
            ->pluck('id')
            ->toArray();

        if (!empty($bookingIds)) {
            DB::table('returns')->whereIn('booking_id', $bookingIds)->delete();
            DB::table('payments')->whereIn('booking_id', $bookingIds)->delete();
            DB::table('bookings')->where('vehicle_id', $id)->delete();
        }

        DB::table('vehicles')->where('id', $id)->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        return redirect()->route('booking.index')->with('success', 'Vehicle deleted successfully!');
    }

    /*
    |--------------------------------------------------------------------------
    | VEHICLES JSON
    | - AJAX endpoint for vehicle list in Add Rent modal
    |--------------------------------------------------------------------------
    */
    public function vehiclesJson(Request $request)
    {
        $query = Vehicle::query();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('plate_number', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->status === 'tersedia') {
            $query->where('status', 'available');
        } elseif ($request->status === 'disewa') {
            $query->where('status', 'rented');
        }

        $vehicles = $query->orderByRaw("FIELD(type, 'scooter', 'sport', 'trail')")
                  ->orderBy('name', 'asc')
                  ->paginate(7);

        return response()->json([
            'data'         => $vehicles->items(),
            'current_page' => $vehicles->currentPage(),
            'last_page'    => $vehicles->lastPage(),
            'total'        => $vehicles->total(),
            'from'         => $vehicles->firstItem() ?? 0,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | PRIVATE HELPER
    | - Convert stored image to base64 data URI
    |--------------------------------------------------------------------------
    */
    private function getImageDataUri($folder, $fileName)
    {
        if (!$fileName) return null;

        $path = $folder . '/' . $fileName;

        if (!Storage::disk('public')->exists($path)) return null;

        $content = Storage::disk('public')->get($path);
        $mime    = Storage::disk('public')->mimeType($path);

        return 'data:' . $mime . ';base64,' . base64_encode($content);
    }
}