<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\ReturnVehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    /**
     * Menampilkan daftar kendaraan dan data pengembalian
     * - Mendukung pencarian berdasarkan nama dan filter tipe
     */
    public function index(Request $request)
    {
        $query = Vehicle::with('bookings');

        // Filter berdasarkan nama motor
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter berdasarkan tipe (scooter, sport, adventure)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $vehicles = $query->get();

        // Ambil semua data pengembalian untuk tabel Return Data
        $returns = ReturnVehicle::with('booking.vehicle')->latest()->get();

        return view('vehicles.index', compact('vehicles', 'returns'));
    }

    /**
     * Menampilkan form tambah motor baru
     */
    public function create()
    {
        return view('vehicles.create');
    }

    /**
     * Menyimpan motor baru ke database
     * - Validasi semua input termasuk foto
     * - Upload foto ke folder public/image/
     * - Status default 'available'
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:100',
            'type'          => 'required|string',
            'plate_number'  => 'required|string|unique:vehicles,plate_number',
            'price_per_day' => 'required|numeric|min:0',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Upload foto motor ke public/image/ (jika ada)
        $imageName = null;
        if ($request->hasFile('image')) {
            $file      = $request->file('image');
            $imageName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('image'), $imageName);
        }

        Vehicle::create([
            'name'          => $request->name,
            'type'          => $request->type,
            'plate_number'  => $request->plate_number,
            'price_per_day' => $request->price_per_day,
            'status'        => 'available', // Default status baru = tersedia
            'image'         => $imageName,
        ]);

        return redirect()->route('vehicles.index')->with('success', 'New motorcycle successfully added!');
    }
}