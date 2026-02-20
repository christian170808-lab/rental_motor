<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::all();
        return view('customers.index', compact('customers'));
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
        $customer->update([
            'customer_name' => $request->customer_name,
            'email'         => $request->email,
            'phone'         => $request->phone,
        ]);
        return redirect()->route('customers.index')->with('success', 'Data customer berhasil diupdate!');
    }

    public function destroy($id)
    {
        Customer::findOrFail($id)->delete();
        return redirect()->route('customers.index')->with('success', 'Customer berhasil dihapus!');
    }
}