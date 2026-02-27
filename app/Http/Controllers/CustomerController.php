<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    | - List all customers with optional search
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
    | - Validate and create a new customer with auto-generated customer_id
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $validated = $this->validateCustomer($request);

        Customer::create([
            ...$validated,
            'customer_id' => $this->generateCustomerId(),
        ]);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer added successfully!');
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    | - Validate and update existing customer data
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, $id)
    {
        $validated = $this->validateCustomer($request, $id);

        Customer::findOrFail($id)->update($validated);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer updated successfully!');
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY
    | - Delete a customer record
    |--------------------------------------------------------------------------
    */
    public function destroy($id)
    {
        Customer::findOrFail($id)->delete();

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer deleted successfully!');
    }

    /*
    |--------------------------------------------------------------------------
    | PRIVATE: VALIDATE CUSTOMER
    | - Reusable validation for store and update
    | - Gmail only, unique email per customer
    |--------------------------------------------------------------------------
    */
    private function validateCustomer(Request $request, $id = null)
    {
        return $request->validate([
            'customer_name' => 'required|string|max:100',
            'email'         => [
                'required',
                'email',
                'regex:/@gmail\.com$/',
                "unique:customers,email,{$id}",
            ],
            'phone_number'  => 'required|string',
            'address'       => 'required|string|max:255',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | PRIVATE: GENERATE CUSTOMER ID
    | - Format: CUST001, CUST002, ...
    |--------------------------------------------------------------------------
    */
    private function generateCustomerId()
    {
        $last   = Customer::latest('id')->first();
        $number = $last ? intval(substr($last->customer_id, 4)) + 1 : 1;

        return 'CUST' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }
}