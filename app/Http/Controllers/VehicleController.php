<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    /**
     * Menampilkan daftar kendaraan dengan fitur pencarian dan filter.
     */
    public function index(Request $request)
    {
        // Memulai query builder pada model Vehicle
        $query = Vehicle::query();

        // Fitur Pencarian berdasarkan Nama Kendaraan
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Fitur Filter berdasarkan Tipe Kendaraan
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Mengambil data berdasarkan query yang sudah difilter
        $vehicles = $query->get();

        // Mengirim data ke view
        return view('vehicles.index', compact('vehicles'));
    }
}