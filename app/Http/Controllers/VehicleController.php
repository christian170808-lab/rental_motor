<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    // Menampilkan daftar kendaraan dengan fitur pencarian dan filter
    public function index(Request $request)
    {
        // Memulai query builder untuk model Vehicle
        $query = Vehicle::query();

        // Filter berdasarkan kata kunci pencarian pada nama kendaraan
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter berdasarkan tipe kendaraan
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Mengambil data berdasarkan query yang sudah difilter
        $vehicles = $query->get();

        // Mengirim data ke view
        return view('vehicles.index', compact('vehicles'));
    }
}