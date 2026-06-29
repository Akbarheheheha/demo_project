<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\PosService;
use Illuminate\Http\Request;
use Exception;

class PosController extends Controller
{
    protected PosService $posService;

    /**
     * PosController constructor.
     *
     * @param PosService $posService
     */
    public function __construct(PosService $posService)
    {
        $this->posService = $posService;
    }

    /**
     * Display a listing of the products for POS.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $products = Product::all();
        return view('pos.index', compact('products'));
    }

    /**
     * Store a newly created transaction in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|integer|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        try {
            $transaction = $this->posService->processCheckout($request->input('items'));
            return redirect()->back()->with('success', 'Transaksi Berhasil! Nomor Invoice: ' . $transaction->invoice);
        } catch (Exception $e) {
            return redirect()->back()->withInput()->withErrors(['checkout_error' => $e->getMessage()]);
        }
    }
}
