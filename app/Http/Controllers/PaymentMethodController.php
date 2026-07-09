<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Services\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaymentMethodController extends Controller
{
    public function __construct(
        private readonly TenantManager $tenantManager,
    ) {}

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
            'nama_metode' => [
                'required', 'string', 'max:50',
                Rule::unique('payment_methods', 'nama_metode')
                    ->where('store_id', $this->tenantManager->getStoreId()),
            ],
        ]);

        PaymentMethod::create($validated);

        return redirect()->route($this->rolePrefix() . '.payment-methods.index')
            ->with('success', 'Metode pembayaran baru berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $validated = $request->validate([
            'nama_metode' => [
                'required', 'string', 'max:50',
                Rule::unique('payment_methods', 'nama_metode')
                    ->where('store_id', $this->tenantManager->getStoreId())
                    ->ignore($paymentMethod->id),
            ],
            'is_active' => 'required|boolean',
        ]);

        $paymentMethod->update($validated);

        return redirect()->route($this->rolePrefix() . '.payment-methods.index')
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

        return redirect()->route($this->rolePrefix() . '.payment-methods.index')
            ->with('success', 'Status keaktifan metode pembayaran berhasil diubah.');
    }

    /**
     * Store a newly created resource in storage via API.
     */
    public function storeApi(Request $request)
    {
        $validated = $request->validate([
            'nama_metode' => [
                'required', 'string', 'max:50',
                Rule::unique('payment_methods', 'nama_metode')
                    ->where('store_id', $this->tenantManager->getStoreId()),
            ],
        ]);

        $validated['is_active'] = true;

        $method = PaymentMethod::create($validated);

        return response()->json($method, 201);
    }

    /**
     * Toggle active status via API.
     */
    public function toggleActiveApi(PaymentMethod $paymentMethod)
    {
        $paymentMethod->update([
            'is_active' => !$paymentMethod->is_active
        ]);

        return response()->json([
            'success' => true,
            'is_active' => (bool)$paymentMethod->is_active
        ]);
    }

    /**
     * Delete payment method via API.
     */
    public function destroyApi(PaymentMethod $paymentMethod)
    {
        $paymentMethod->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
