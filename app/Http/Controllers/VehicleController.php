<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\ReturnVehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehicle::with('bookings');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $vehicles = $query->get();
    $returns  = ReturnVehicle::with('booking.vehicle')->latest()->get(); // ← tambah ini

    return view('vehicles.index', compact('vehicles', 'returns')); // ← tambah returns
    }

    public function create()
{
    return view('vehicles.create');
}

public function store(Request $request)
{
    $request->validate([
        'name'         => 'required|string|max:100',
        'type'         => 'required|string',
        'plate_number' => 'required|string|unique:vehicles,plate_number',
        'price_per_day'=> 'required|numeric|min:0',
        'image'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
    ]);

    $imageName = null;
    if ($request->hasFile('image')) {
        $file = $request->file('image');
        $imageName = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('image'), $imageName);
    }

    Vehicle::create([
        'name'          => $request->name,
        'type'          => $request->type,
        'plate_number'  => $request->plate_number,
        'price_per_day' => $request->price_per_day,
        'status'        => 'available',
        'image'         => $imageName,
    ]);

    return redirect()->route('vehicles.index')->with('success', 'Motor baru berhasil ditambahkan!');
}

}