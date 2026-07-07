<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $methods = PaymentMethod::all();
        return view('admin.payment_methods.index', compact('methods'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_metode' => 'required|string|max:50|unique:payment_methods,nama_metode',
        ]);

        PaymentMethod::create($validated);

        return redirect()->route('payment-methods.index')
            ->with('success', 'Metode pembayaran baru berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $validated = $request->validate([
            'nama_metode' => 'required|string|max:50|unique:payment_methods,nama_metode,' . $paymentMethod->id,
            'is_active' => 'required|boolean',
        ]);

        $paymentMethod->update($validated);

        return redirect()->route('payment-methods.index')
            ->with('success', 'Metode pembayaran berhasil diperbarui.');
    }

    /**
     * Toggle the active status of the specified resource.
     */
    public function toggleActive(PaymentMethod $paymentMethod)
    {
        $paymentMethod->update([
            'is_active' => !$paymentMethod->is_active
        ]);

        return redirect()->route('payment-methods.index')
            ->with('success', 'Status keaktifan metode pembayaran berhasil diubah.');
    }
}
