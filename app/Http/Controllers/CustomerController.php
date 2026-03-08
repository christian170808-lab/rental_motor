<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Booking;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $customers = Customer::query()
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('customer_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone_number', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10);

        return view('customers.index', compact('customers'));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $validated = $this->validateCustomer($request);

        $ktpName = null;
        if ($request->hasFile('ktp_photo')) {
            $file    = $request->file('ktp_photo');
            $ktpName = time() . '_ktp_' . $file->getClientOriginalName();
            $file->move(public_path('ktp'), $ktpName);
        }

        Customer::create([
            ...$validated,
            'customer_id' => $this->generateCustomerId(),
            'ktp_photo'   => $ktpName,
        ]);

        return redirect()->route('customers.index')->with('success', 'Customer added successfully!');
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, $id)
    {
        $customer  = Customer::findOrFail($id);
        $validated = $this->validateCustomer($request, $id);

        if ($request->hasFile('ktp_photo')) {
            if ($customer->ktp_photo && file_exists(public_path('ktp/' . $customer->ktp_photo))) {
                unlink(public_path('ktp/' . $customer->ktp_photo));
            }
            $file    = $request->file('ktp_photo');
            $ktpName = time() . '_ktp_' . $file->getClientOriginalName();
            $file->move(public_path('ktp'), $ktpName);
            $validated['ktp_photo'] = $ktpName;
        }

        $customer->update($validated);

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully!');
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY
    |--------------------------------------------------------------------------
    */
    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);

        // Cek apakah customer masih punya booking aktif (belum returned)
        $activeBooking = Booking::where('customer_id', $id)
            ->whereDoesntHave('returnVehicle')
            ->exists();

        if ($activeBooking) {
            return redirect()->route('customers.index')
                ->with('error', 'Cannot delete "' . $customer->customer_name . '" because they currently have an active rental.');
        }

        if ($customer->ktp_photo && file_exists(public_path('ktp/' . $customer->ktp_photo))) {
            unlink(public_path('ktp/' . $customer->ktp_photo));
        }

        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully!');
    }

    /*
    |--------------------------------------------------------------------------
    | PRIVATE: VALIDATE
    |--------------------------------------------------------------------------
    */
    private function validateCustomer(Request $request, $id = null)
    {
        return $request->validate([
            'customer_name' => 'required|string|max:100',
            'email'         => [
                'required', 'email', 'regex:/@gmail\.com$/',
                "unique:customers,email,{$id}",
            ],
            'phone_number'  => 'required|string',
            'address'       => 'required|string|max:255',
            'ktp_photo'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | PRIVATE: GENERATE CUSTOMER ID
    |--------------------------------------------------------------------------
    */
    private function generateCustomerId()
    {
        $last   = Customer::latest('id')->first();
        $number = $last ? intval(substr($last->customer_id, 4)) + 1 : 1;
        return 'CUST' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }
}