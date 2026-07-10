<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $expenses = Expense::orderBy('tanggal', 'desc')->paginate(10);
        $totalPengeluaran = Expense::sum('nominal');
        
        return view('admin.expenses.index', compact('expenses', 'totalPengeluaran'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('expenses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'nama_pengeluaran' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
        ]);

        Expense::create($validated);

        return redirect()->route($this->rolePrefix() . '.expenses.index')
            ->with('success', 'Pengeluaran berhasil dicatat.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Expense $expense)
    {
        return view('expenses.edit', compact('expense'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'nama_pengeluaran' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
        ]);

        $expense->update($validated);

        return redirect()->route($this->rolePrefix() . '.expenses.index')
            ->with('success', 'Catatan pengeluaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        $expense->delete();

        return redirect()->route($this->rolePrefix() . '.expenses.index')
            ->with('success', 'Catatan pengeluaran berhasil dihapus.');
    }

    /**
     * Store a newly created resource in storage via API.
     */
    public function storeApi(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'nama_pengeluaran' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
        ]);

        $expense = Expense::create($validated);

        return response()->json($expense, 201);
    }

    /**
     * Remove the specified resource from storage via API.
     */
    public function destroyApi(Expense $expense)
    {
        $expense->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
