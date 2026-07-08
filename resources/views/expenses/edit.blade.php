@extends('layouts.app')

@section('title', 'Edit Pengeluaran')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Edit Pengeluaran</h1>

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route($rolePrefix . '.expenses.update', $expense) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                <input type="date" id="tanggal" name="tanggal" value="{{ old('tanggal', $expense->tanggal) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div>
                <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <input type="text" id="deskripsi" name="deskripsi" value="{{ old('deskripsi', $expense->deskripsi) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div>
                <label for="nominal" class="block text-sm font-medium text-gray-700 mb-1">Nominal (Rp)</label>
                <input type="number" id="nominal" name="nominal" value="{{ old('nominal', $expense->nominal) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required min="0" step="0.01">
            </div>
            <div class="flex items-center justify-between">
                <a href="{{ route($rolePrefix . '.expenses.index') }}" class="btn btn-secondary">Kembali</a>
                <button type="submit" class="btn btn-primary">Perbarui Pengeluaran</button>
            </div>
        </form>
    </div>
</div>
@endsection