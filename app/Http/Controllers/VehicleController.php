<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\ReturnVehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class VehicleController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    | - List vehicles with search and type filter
    | - Also shows return history
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

        $vehicles = $query->orderBy('type')->paginate(7);

        $returns = ReturnVehicle::with('booking.vehicle')->latest()->paginate(5, ['*'], 'page_returns');

        return view('vehicles.index', compact('vehicles', 'returns'));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    | - Validate and create a new vehicle with photo upload
    | - Plate format: XX 1234 XXX
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:100',
            'type'          => 'required|in:scooter,sport,trail',
            'plate_number'  => [
                'required',
                'unique:vehicles,plate_number',
                'regex:/^[A-Za-z]{1,3}\s?\d{1,4}\s?[A-Za-z]{1,3}$/',
            ],
            'price_per_day' => 'required|numeric|min:0',
            'image'         => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $imageName = null;
        if ($request->hasFile('image')) {
            $file      = $request->file('image');
            $imageName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('image'), $imageName);
        }

        Vehicle::create([
            'name'          => $request->name,
            'type'          => strtolower($request->type),
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
    | - Update vehicle data, optionally replace photo
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        $request->validate([
            'name'          => 'required|string|max:100',
            'type'          => 'required|in:scooter,sport,trail',
            'plate_number'  => [
                'required',
                'regex:/^[A-Z]{1,3} \d{1,4} [A-Z]{1,3}$/i',
                Rule::unique('vehicles', 'plate_number')->ignore($vehicle->id),
            ],
            'price_per_day' => 'required|numeric|min:0',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $file            = $request->file('image');
            $imageName       = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('image'), $imageName);
            $vehicle->image  = $imageName;
        }

        $vehicle->update([
            'name'          => $request->name,
            'type'          => strtolower($request->type),
            'plate_number'  => strtoupper($request->plate_number),
            'price_per_day' => $request->price_per_day,
            'image'         => $vehicle->image,
        ]);

        return redirect()->route('vehicles.index')->with('success', 'Vehicle updated successfully!');
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY
    | - Delete vehicle and cascade delete bookings, returns, and payments
    |--------------------------------------------------------------------------
    */
    public function destroy($id)
    {
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
}