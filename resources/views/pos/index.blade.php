@extends('layouts.app')

@section('title', 'Kasir POS')
@section('active_page', 'pos')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row gap-6">
        
        <!-- Kolom Kiri: Informasi dan Petunjuk -->
        <div class="w-full md:w-2/3 bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">Daftar Produk (Kasir POS)</h2>
            
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('pos.store') }}" method="POST" id="checkout-form">
                @csrf
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pilih</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Produk</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah (Qty)</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($products as $index => $product)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <input type="checkbox" name="items[{{ $index }}][id]" value="{{ $product->id }}" class="product-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $product->sku }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $product->stock < 10 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                            {{ $product->stock }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <input type="number" name="items[{{ $index }}][qty]" min="1" max="{{ $product->stock }}" value="1" disabled class="qty-input w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-gray-100 text-gray-400">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Tidak ada produk tersedia. Silakan jalankan seeder atau tambah produk terlebih dahulu.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded-lg transition duration-200 shadow-md">
                        BAYAR SEKARANG & SUBMIT PEMBAYARAN
                    </button>
                </div>
            </form>
        </div>

        <!-- Kolom Kanan: Summary -->
        <div class="w-full md:w-1/3 bg-white shadow rounded-lg p-6 self-start">
            <h3 class="text-lg font-semibold mb-4 text-gray-800">Petunjuk Penggunaan POS</h3>
            <div class="text-sm text-gray-600 space-y-3">
                <p>1. Beri tanda centang pada checkbox di kolom <strong>"Pilih"</strong> untuk produk yang akan dibeli.</p>
                <p>2. Kolom input <strong>"Jumlah (Qty)"</strong> akan otomatis aktif setelah Anda mencentang produk tersebut.</p>
                <p>3. Masukkan jumlah kuantitas produk yang diinginkan.</p>
                <p>4. Klik tombol <strong>"BAYAR SEKARANG & SUBMIT PEMBAYARAN"</strong> untuk memproses checkout transaksi secara atomik menggunakan database transaction.</p>
            </div>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkboxes = document.querySelectorAll('.product-checkbox');
        
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                const row = this.closest('tr');
                const qtyInput = row.querySelector('.qty-input');
                
                if (this.checked) {
                    qtyInput.disabled = false;
                    qtyInput.classList.remove('bg-gray-100', 'text-gray-400');
                    qtyInput.classList.add('bg-white', 'text-gray-900');
                } else {
                    qtyInput.disabled = true;
                    qtyInput.classList.add('bg-gray-100', 'text-gray-400');
                    qtyInput.classList.remove('bg-white', 'text-gray-900');
                }
            });
        });
    });
</script>
@endsection
