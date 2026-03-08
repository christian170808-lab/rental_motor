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
                        ->latest()  
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
        })
        ->withMax('bookings', 'id')
        ->orderByDesc('bookings_max_id')
        ->paginate(10);
        $vehicleTypes = \App\Models\VehicleType::orderBy('label')->get();

        return view('booking.index', compact('vehicles', 'customers', 'rentedVehicles', 'vehicleTypes'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
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
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id'           => ['required', 'exists:vehicles,id'],
            'customer_id'          => ['required', 'exists:customers,id'],
            'identity_card'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'identity_card_base64' => ['nullable', 'string'],
            'payment_proof'        => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'start_date'           => ['required', 'date'],
            'end_date'             => ['required', 'date', 'after_or_equal:start_date'],
            'payment_type'         => ['required', 'in:dp,full'],
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
        } elseif ($request->filled('identity_card_base64')) {
            $base64Data = $request->identity_card_base64;
            $imageData  = preg_replace('#^data:image/\w+;base64,#i', '', $base64Data);
            $decoded    = base64_decode($imageData);
            $ktpName    = time() . '_ktp_auto_' . $customerId . '.jpg';
            Storage::disk('public')->put('ktp/' . $ktpName, $decoded);
        }

        if (!$ktpName) {
            $customer = Customer::findOrFail($customerId);
            $ktpName  = $customer->ktp_photo;
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
    |--------------------------------------------------------------------------
    */
    public function show($id)
    {
        $booking = Booking::with(['vehicle', 'customer', 'returnVehicle'])->findOrFail($id);

        if ($booking->payment_status !== 'paid') {
            $booking->update(['payment_type' => 'full']);
        }

        $ktpDataUri   = $this->getImageDataUri('ktp', $booking->identity_card);
        $proofDataUri = $this->getImageDataUri('payments', $booking->payment_proof);

        return view('booking.show', compact('booking', 'ktpDataUri', 'proofDataUri'));
    }

    /*
    |--------------------------------------------------------------------------
    | GET CUSTOMER (AJAX)
    |--------------------------------------------------------------------------
    */
    public function getCustomer($id)
    {
        $customer = Customer::findOrFail($id);
        return response()->json([
            'customer_name' => $customer->customer_name,
            'phone_number'  => $customer->phone_number,
            'address'       => $customer->address,
            'email'         => $customer->email,
            'ktp_photo'     => $customer->ktp_photo,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
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
    |--------------------------------------------------------------------------
    */
    public function edit($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        return view('booking.edit', compact('vehicle'));
    }

    /*
    |--------------------------------------------------------------------------
    | RETURN VEHICLE
    |--------------------------------------------------------------------------
    */
    public function returnVehicle($id)
    {
        $booking = Booking::findOrFail($id);

        DB::transaction(function () use ($booking) {
            $booking->update(['payment_type' => 'full']);
        });

        return redirect()->route('booking.index')->with('success', 'Vehicle returned successfully!');
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY
    |--------------------------------------------------------------------------
    */
    public function destroy(Request $request, $id)
    {
        $booking = Booking::with(['vehicle', 'customer'])->findOrFail($id);
        $vehicle = $booking->vehicle;

        // Simpan ke cancel history
        \App\Models\Cancellation::create([
            'customer_name'  => $booking->customer->customer_name ?? '—',
            'vehicle_name'   => $vehicle->name ?? '—',
            'plate_number'   => $vehicle->plate_number ?? '—',
            'reason'         => $request->input('cancel_reason', '—'),
            'cancelled_date' => now()->toDateString(),
        ]);

        // Update status booking jadi cancelled (tidak dihapus agar muncul di report)
        $booking->update(['payment_status' => 'cancelled']);

        // Kembalikan status kendaraan ke available
        if ($vehicle) {
            $vehicle->update(['status' => 'available']);
        }

        if ($request->input('redirect_to') === 'cancel_history') {
            return redirect()->route('vehicles.index', ['tab' => 'cancel'])
                            ->with('success', 'Rental cancelled successfully.');
        }

        return redirect()->route('booking.index')->with('success', 'Rental cancelled successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | VEHICLES JSON
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

        $vehicles = $query->orderBy('name', 'asc')->paginate(7);

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