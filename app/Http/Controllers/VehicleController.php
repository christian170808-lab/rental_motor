<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Models\ReturnVehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class VehicleController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $query = Vehicle::with('bookings.customer');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('plate_number', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $vehicles = $query->orderByRaw("FIELD(status, 'rented') DESC")->latest()->paginate(7);
        $vehicleTypes = VehicleType::orderBy('label')->get()->map(function ($vt) {
            $vt->vehicles_count = \App\Models\Vehicle::whereRaw(
                'CONVERT(type USING utf8mb4) COLLATE utf8mb4_unicode_ci = CONVERT(? USING utf8mb4) COLLATE utf8mb4_unicode_ci',
                [$vt->name]
            )->count();
            return $vt;
        });
        $returns      = ReturnVehicle::with('booking.vehicle')->latest()->paginate(5, ['*'], 'page_returns');

        $cancellations = \App\Models\Cancellation::latest()->paginate(10, ['*'], 'page_cancel');

return view('vehicles.index', compact('vehicles','returns','vehicleTypes','cancellations'));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:100',
            'type'          => 'required|string|max:50',
            'plate_number'  => [
                'required',
                'unique:vehicles,plate_number',
                'regex:/^[A-Za-z]{1,3}\s?\d{1,4}\s?[A-Za-z]{1,3}$/',
            ],
            'price_per_day' => 'required|numeric|min:0',
            'image'         => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $typeName = strtolower(preg_replace('/\s+/', '_', trim($request->type)));
        VehicleType::firstOrCreate(
            ['name'  => $typeName],
            ['label' => ucfirst(trim($request->type))]
        );

        $imageName = null;
        if ($request->hasFile('image')) {
            $file      = $request->file('image');
            $imageName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('image'), $imageName);
        }

        Vehicle::create([
            'name'          => $request->name,
            'type'          => $typeName,
            'plate_number'  => strtoupper($request->plate_number),
            'price_per_day' => $request->price_per_day,
            'status'        => 'available',
            'image'         => $imageName,
        ]);

        return redirect()->route('vehicles.index')->with('success', 'Vehicle added successfully!');
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        $request->validate([
            'name'          => 'required|string|max:100',
            'type'          => 'required|string|max:50',
            'plate_number'  => [
                'required',
                'regex:/^[A-Z]{1,3} \d{1,4} [A-Z]{1,3}$/i',
                Rule::unique('vehicles', 'plate_number')->ignore($vehicle->id),
            ],
            'price_per_day' => 'required|numeric|min:0',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $typeName = strtolower(preg_replace('/\s+/', '_', trim($request->type)));
        VehicleType::firstOrCreate(
            ['name'  => $typeName],
            ['label' => ucfirst(trim($request->type))]
        );

        if ($request->hasFile('image')) {
            $file           = $request->file('image');
            $imageName      = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('image'), $imageName);
            $vehicle->image = $imageName;
        }

        $vehicle->update([
            'name'          => $request->name,
            'type'          => $typeName,
            'plate_number'  => strtoupper($request->plate_number),
            'price_per_day' => $request->price_per_day,
            'image'         => $vehicle->image,
        ]);

        return redirect()->route('vehicles.index')->with('success', 'Vehicle updated successfully!');
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY
    |--------------------------------------------------------------------------
    */
    public function destroy($id)
    {
        $vehicle = Vehicle::findOrFail($id);

        if ($vehicle->status === 'rented') {
            return redirect()->route('vehicles.index')
                ->with('error', 'Cannot delete: vehicle "' . $vehicle->name . '" is currently rented.');
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $bookingIds = DB::table('bookings')->where('vehicle_id', $id)->pluck('id')->toArray();

        if (!empty($bookingIds)) {
            DB::table('returns')->whereIn('booking_id', $bookingIds)->delete();
            DB::table('payments')->whereIn('booking_id', $bookingIds)->delete();
            DB::table('bookings')->where('vehicle_id', $id)->delete();
        }

        DB::table('vehicles')->where('id', $id)->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        return redirect()->route('vehicles.index')->with('success', 'Vehicle deleted successfully!');
    }

    /*
    |--------------------------------------------------------------------------
    | VEHICLE TYPES — list (untuk panel manage types, include vehicles_count)
    |--------------------------------------------------------------------------
    */
    public function typeList()
    {
        $types = VehicleType::orderBy('label')->get()->map(function ($vt) {
            $vt->vehicles_count = \App\Models\Vehicle::whereRaw(
                'CONVERT(type USING utf8mb4) COLLATE utf8mb4_unicode_ci = CONVERT(? USING utf8mb4) COLLATE utf8mb4_unicode_ci',
                [$vt->name]
            )->count();
            return $vt;
        });
        return response()->json($types);
    }

    /*
    |--------------------------------------------------------------------------
    | VEHICLE TYPES — index
    |--------------------------------------------------------------------------
    */
    public function typeIndex()
    {
        $types = VehicleType::orderBy('label')->get();
        return response()->json($types);
    }

    /*
    |--------------------------------------------------------------------------
    | VEHICLE TYPES — store (AJAX)
    |--------------------------------------------------------------------------
    */
    public function typeStore(Request $request)
    {
        $request->validate([
            'label' => 'required|string|max:50',
        ]);

        $name = strtolower(preg_replace('/\s+/', '_', trim($request->label)));

        if (VehicleType::where('name', $name)->exists()) {
            return response()->json(['error' => 'Type "' . $request->label . '" already exists.'], 422);
        }

        $type = VehicleType::create([
            'name'  => $name,
            'label' => ucwords(trim($request->label)),
        ]);

        return response()->json($type, 201);
    }

    /*
    |--------------------------------------------------------------------------
    | VEHICLE TYPES — destroy (AJAX)
    | Hapus type + semua kendaraan yang memakai type ini beserta relasi booking/return/payment
    |--------------------------------------------------------------------------
    */
    public function typeDestroy($id)
    {
        $type = VehicleType::findOrFail($id);

        $vehicleIds = Vehicle::where('type', $type->name)->pluck('id')->toArray();
        $vehicleCount = count($vehicleIds);

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        if (!empty($vehicleIds)) {
            $bookingIds = DB::table('bookings')->whereIn('vehicle_id', $vehicleIds)->pluck('id')->toArray();
            if (!empty($bookingIds)) {
                DB::table('returns')->whereIn('booking_id', $bookingIds)->delete();
                DB::table('payments')->whereIn('booking_id', $bookingIds)->delete();
                DB::table('bookings')->whereIn('id', $bookingIds)->delete();
            }
            DB::table('vehicles')->whereIn('id', $vehicleIds)->delete();
        }

        $type->delete();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        return response()->json([
            'name'          => $type->name,
            'vehicle_count' => $vehicleCount,
            'message'       => 'Type deleted.',
        ]);
    }
}