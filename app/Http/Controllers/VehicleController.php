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
}