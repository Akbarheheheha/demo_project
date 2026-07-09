<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Setting;
use App\Models\Transaction;
use App\Services\PosService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
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
        $categories = \App\Models\Category::pluck('name')->toArray();
        $paymentMethods = \App\Models\PaymentMethod::where('is_active', true)->get();
        $defaultTaxPercent = (int) Setting::get('tax_percent', 11);
        $defaultDiscount = (int) Setting::get('default_discount', 0);
        $receiptSize = Setting::get('receipt_size', '58mm');

        return view('pos.index', compact('products', 'categories', 'paymentMethods', 'defaultTaxPercent', 'defaultDiscount', 'receiptSize'));
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
            'cash_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|string|exists:payment_methods,nama_metode',
        ]);

        try {
            $transaction = $this->posService->processCheckout(
                $request->input('items'),
                $request->input('customer_name'),
                (float) $request->input('discount_percent', 0),
                (float) $request->input('tax_percent', 11),
                $request->input('payment_method', 'Tunai')
            );

            $cacheKey = sprintf(
                'reports:sales-summary:%s:%s',
                Carbon::now()->startOfMonth()->toDateString(),
                Carbon::now()->endOfMonth()->toDateString()
            );

            Cache::forget($cacheKey);

            $cashAmount = (float) $request->input('cash_amount', $transaction->total_harga);

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Transaksi Berhasil! Nomor Invoice: '.$transaction->invoice,
                    'print_url' => route('pos.receipt', $transaction->id) . '?cash=' . $cashAmount
                ]);
            }

            return redirect()->back()->with([
                'success' => 'Transaksi Berhasil! Nomor Invoice: '.$transaction->invoice,
                'print_url' => route('pos.receipt', $transaction->id) . '?cash=' . $cashAmount
            ]);
        } catch (Exception $e) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }

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

        $cashReceived = (float) request()->query('cash', $transaction->total_harga);
        $change = max(0, $cashReceived - $transaction->total_harga);
        $receiptSize = Setting::get('receipt_size', '58mm');
        $shopName = Setting::get('store_name', 'Kios Berkah Raya');
        $shopAddress = Setting::get('store_address', '');
        $shopPhone = Setting::get('store_phone', '');

        return view('pos.receipt', [
            'invoice' => $transaction->invoice,
            'date' => $transaction->created_at->format('d/m/Y H:i:s'),
            'cashier' => $transaction->user->name ?? 'Kasir',
            'customer_name' => $transaction->customer_name,
            'paymentMethod' => $transaction->payment_method,
            'items' => $items,
            'subtotal' => $subtotal,
            'discount' => (float) $transaction->discount,
            'tax' => (float) $transaction->tax,
            'grandTotal' => (float) $transaction->total_harga,
            'cashReceived' => $cashReceived,
            'change' => $change,
            'receiptSize' => $receiptSize,
            'shopName' => $shopName,
            'shopAddress' => $shopAddress,
            'shopPhone' => $shopPhone,
        ]);
    }
}
