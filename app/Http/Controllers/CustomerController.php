<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Menampilkan daftar semua customer
     */
    public function index()
    {
        $customers = Customer::all();
        return view('customers.index', compact('customers'));
    }

    /**
     * Menampilkan form edit customer berdasarkan ID
     */
    public function edit($id)
    {
        $customer = Customer::findOrFail($id); // Otomatis 404 jika tidak ditemukan
        return view('customers.edit', compact('customer'));
    }

    /**
     * Menyimpan perubahan data customer
     * FIX: Kolom 'phone' diubah ke 'phone_number' sesuai migration
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'customer_name' => ['required', 'string', 'max:100'],
            'email'         => ['required', 'email'],
            'phone'         => ['required', 'string'],
        ]);

        $customer = Customer::findOrFail($id);
        $customer->update([
            'customer_name' => $request->customer_name,
            'email'         => $request->email,
            'phone'         => $request->phone, // Column name in database is 'phone'
        ]);

        return redirect()->route('customers.index')->with('success', 'Data customer berhasil diupdate!');
    }

    /**
     * Menghapus customer berdasarkan ID
     */
    public function destroy($id)
    {
        Customer::findOrFail($id)->delete();
        return redirect()->route('customers.index')->with('success', 'Customer berhasil dihapus!');
    }
}