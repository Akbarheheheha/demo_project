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
        return view('admin.expenses.index', compact('expenses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.expenses.create');
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
        ]);

        Expense::create($validated);

        return redirect()->route('expenses.index')
            ->with('success', 'Pengeluaran berhasil dicatat.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Expense $expense)
    {
        return view('admin.expenses.edit', compact('expense'));
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
        ]);

        $expense->update($validated);

        return redirect()->route('expenses.index')
            ->with('success', 'Catatan pengeluaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        $expense->delete();

        return redirect()->route('expenses.index')
            ->with('success', 'Catatan pengeluaran berhasil dihapus.');
    }
}
