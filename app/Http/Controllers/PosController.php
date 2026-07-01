<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Services\PosService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class PosController extends Controller
{
    protected PosService $posService;

    /**
     * PosController constructor.
     */
    public function __construct(PosService $posService)
    {
        $this->posService = $posService;
    }

    /**
     * Open the POS workspace outside the admin SPA shell.
     *
     * @return View
     */
    public function launcher()
    {
        return view('pos.launcher');
    }

    /**
     * Display a listing of the products for the full-page POS.
     *
     * @return View
     */
    public function index()
    {
        $products = Product::all();
        $categories = Product::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->pluck('category')
            ->toArray();

        return view('pos.index', compact('products', 'categories'));
    }

    /**
     * Store a newly created transaction in storage.
     *
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|integer|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'customer_name' => 'nullable|string|max:255',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'tax_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        try {
            $transaction = $this->posService->processCheckout(
                $request->input('items'),
                $request->input('customer_name'),
                (float) $request->input('discount_percent', 0),
                (float) $request->input('tax_percent', 11)
            );

            Cache::tags(['reports', 'sales-summary'])->flush();

            return redirect()->back()->with([
                'success' => 'Transaksi Berhasil! Nomor Invoice: '.$transaction->invoice,
                'print_url' => route('pos.receipt', $transaction->id)
            ]);
        } catch (Exception $e) {
            return redirect()->back()->withInput()->withErrors(['checkout_error' => $e->getMessage()]);
        }
    }

    /**
     * View the thermal print receipt.
     *
     * @param Transaction $transaction
     * @return View
     */
    public function receipt(Transaction $transaction): View
    {
        $transaction->load(['details.product', 'user']);
        
        $subtotal = $transaction->details->sum(function ($detail) {
            return $detail->harga_jual * $detail->qty;
        });

        $items = $transaction->details->map(function ($detail) {
            return [
                'name' => $detail->product->name,
                'qty' => $detail->qty,
                'price' => (float) $detail->harga_jual,
                'total' => (float) $detail->subtotal,
            ];
        })->toArray();

        return view('pos.receipt', [
            'invoice' => $transaction->invoice,
            'date' => $transaction->created_at->format('d/m/Y H:i:s'),
            'cashier' => $transaction->user->name ?? 'Kasir',
            'paymentMethod' => 'TUNAI',
            'items' => $items,
            'subtotal' => $subtotal,
            'discount' => (float) $transaction->discount,
            'tax' => (float) $transaction->tax,
            'grandTotal' => (float) $transaction->total_harga,
            'cashReceived' => (float) $transaction->total_harga,
            'change' => 0.0
        ]);
    }
}
