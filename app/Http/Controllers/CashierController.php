<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CashierController extends Controller
{
    /**
     * Display a listing of cashiers.
     */
    public function index()
    {
        // Query users with the role 'Kasir'
        $cashiers = User::role('Kasir')->paginate(10);

        return view('admin.cashiers.index', compact('cashiers'));
    }

    /**
     * Show the form for creating a new cashier.
     */
    public function create()
    {
        return view('admin.cashiers.create');
    }

    /**
     * Store a newly created cashier in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $cashier = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign 'Kasir' role using Spatie Permission
        $cashier->assignRole('Kasir');

        return redirect()->route($this->rolePrefix() . '.cashiers.index')
            ->with('success', 'Akun Kasir berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified cashier.
     */
    public function edit($id)
    {
        $cashier = User::role('Kasir')->findOrFail($id);

        return view('admin.cashiers.edit', compact('cashier'));
    }

    /**
     * Update the specified cashier in storage.
     */
    public function update(Request $request, $id)
    {
        $cashier = User::role('Kasir')->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $cashier->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $cashier->update($data);

        return redirect()->route($this->rolePrefix() . '.cashiers.index')
            ->with('success', 'Akun Kasir berhasil diperbarui.');
    }

    /**
     * Remove the specified cashier from storage.
     */
    public function destroy($id)
    {
        $cashier = User::role('Kasir')->findOrFail($id);
        $cashier->delete();

        return redirect()->route($this->rolePrefix() . '.cashiers.index')
            ->with('success', 'Akun Kasir berhasil dihapus.');
    }
}
