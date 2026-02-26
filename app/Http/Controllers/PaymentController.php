<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;

class PaymentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    | - List paginated payments with revenue stats
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $payments = Payment::with(['customer', 'vehicle', 'booking'])
            ->latest()
            ->paginate(10);

        return view('Payment', [
            'payments'          => $payments,
            'totalRevenue'      => $this->totalRevenue(),
            'totalTransactions' => $this->totalTransactions(),
        ]);
    }

        public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW
    | - Display a single payment detail
    |--------------------------------------------------------------------------
    */
    public function show($id)
    {
        $payment = Payment::with(['customer', 'vehicle'])->findOrFail($id);

        return view('payments.show', compact('payment'));
    }

    /*
    |--------------------------------------------------------------------------
    | PRIVATE: TOTAL REVENUE
    | - Sum of all paid payments
    |--------------------------------------------------------------------------
    */
    private function totalRevenue()
    {
        return Payment::where('status', 'paid')->sum('total_price');
    }

    /*
    |--------------------------------------------------------------------------
    | PRIVATE: TOTAL TRANSACTIONS
    | - Count of all paid transactions
    |--------------------------------------------------------------------------
    */
    private function totalTransactions()
    {
        return Payment::where('status', 'paid')->count();
    }
}