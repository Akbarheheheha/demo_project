<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Belanja - #{{ $invoice ?? 'TRX-20260701-999' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Fallback Tailwind CDN in case Vite is not active or during standalone popup printing -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body {
                background-color: #ffffff;
                color: #000000;
                margin: 0;
                padding: 0;
                font-size: 11px;
            }
            .no-print {
                display: none !important;
            }
            /* Hide general layout components if embedded */
            header, footer, aside, nav, #sidebar, .sidebar-animate-bg, #spa-progressbar {
                display: none !important;
            }
            @page {
                size: 80mm auto;
                margin: 0;
            }
            .print-container {
                width: 80mm;
                max-width: 80mm;
                margin: 0;
                padding: 4mm;
                box-shadow: none !important;
                border: none !important;
            }
        }
        
        /* Stylings for screen view */
        body {
            background-color: #f1f5f9;
        }
    </style>
</head>
<body class="font-mono text-slate-800 antialiased min-h-screen flex flex-col items-center justify-start p-4 sm:p-6">

    <!-- Action Bar (Hidden on Print) -->
    <div class="no-print w-full max-w-[80mm] mb-4 flex gap-2">
        <button onclick="window.history.back()" class="flex-1 py-2 px-3 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-medium rounded-xl text-xs flex items-center justify-center gap-1.5 transition-all shadow-sm">
            &larr; Kembali
        </button>
        <button onclick="window.print()" class="flex-[2] py-2 px-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs flex items-center justify-center gap-1.5 transition-all shadow-md shadow-indigo-650/15">
            Cetak Struk (Print)
        </button>
    </div>

    <!-- Receipt Thermal Paper Container -->
    <div class="print-container w-[80mm] max-w-[80mm] bg-white p-5 border border-slate-200 shadow-sm rounded-2xl flex flex-col text-[11px] leading-relaxed">
        
        <!-- Header / Shop Logo & Info -->
        <div class="text-center space-y-1 mb-4">
            <h1 class="text-sm font-extrabold uppercase tracking-wide text-slate-900">{{ $shop_name ?? 'Kios Berkah Raya' }}</h1>
            <p class="text-[10px] text-slate-500">Jl. Berkah No. 88, Jakarta Selatan</p>
            <p class="text-[9px] text-slate-400">Telp: 0812-3456-7890</p>
        </div>
        
        <!-- Divider (Dashed) -->
        <div class="border-b border-dashed border-slate-300 my-2"></div>
        
        <!-- Metadata Info -->
        <div class="space-y-1 text-slate-600 text-[10px] my-1">
            <div class="flex justify-between">
                <span>No. Transaksi:</span>
                <span class="font-bold text-slate-800">{{ $invoice ?? 'TRX-20260701-085' }}</span>
            </div>
            <div class="flex justify-between">
                <span>Tanggal:</span>
                <span>{{ $date ?? now()->format('d/m/Y H:i:s') }}</span>
            </div>
            <div class="flex justify-between">
                <span>Kasir:</span>
                <span class="capitalize">{{ $cashier ?? (auth()->user()->name ?? 'Administrator') }}</span>
            </div>
            <div class="flex justify-between">
                <span>Metode Bayar:</span>
                <span class="uppercase font-bold text-slate-800">{{ $paymentMethod ?? 'TUNAI' }}</span>
            </div>
        </div>
        
        <!-- Divider (Dashed) -->
        <div class="border-b border-dashed border-slate-300 my-2"></div>
        
        <!-- Items Table Header -->
        <div class="grid grid-cols-12 gap-1 font-bold text-slate-700 text-[10px] mb-1.5 uppercase">
            <div class="col-span-6">Nama Barang</div>
            <div class="col-span-2 text-center">Qty</div>
            <div class="col-span-4 text-right">Subtotal</div>
        </div>
        
        <!-- Items List -->
        <div class="space-y-2">
            @php
                $mockItems = [
                    ['name' => 'Kopi Kapal Api 165g', 'qty' => 2, 'price' => 15000, 'total' => 30000],
                    ['name' => 'Indomie Goreng Spesial', 'qty' => 5, 'price' => 3500, 'total' => 17500],
                    ['name' => 'Susu Ultra Milk Cokelat 1L', 'qty' => 1, 'price' => 19500, 'total' => 19500],
                ];
                $activeItems = $items ?? $mockItems;
            @endphp
            @foreach($activeItems as $item)
                <div>
                    <!-- Item Name (Full Row if long) -->
                    <div class="text-slate-800 font-bold text-[10px]">{{ $item['name'] ?? ($item['product']['name'] ?? 'Produk') }}</div>
                    <!-- Qty x Price & Line Total -->
                    <div class="grid grid-cols-12 gap-1 text-slate-650 text-[10px]">
                        <div class="col-span-6 pl-1.5 text-slate-500">
                            {{ number_format($item['qty'], 0) }} x {{ number_format($item['price'] ?? ($item['product']['price'] ?? 0), 0, ',', '.') }}
                        </div>
                        <div class="col-span-6 text-right font-semibold text-slate-800">
                            Rp {{ number_format($item['total'] ?? (($item['price'] ?? 0) * $item['qty']), 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Divider (Dashed) -->
        <div class="border-b border-dashed border-slate-300 my-3"></div>
        
        <!-- Summary Calculations -->
        <div class="space-y-1.5 text-[10px]">
            <div class="flex justify-between text-slate-600">
                <span>Subtotal</span>
                <span class="font-semibold text-slate-800">Rp {{ number_format($subtotal ?? 67000, 0, ',', '.') }}</span>
            </div>
            @if(($discount ?? 0) > 0 || !isset($discount))
            <div class="flex justify-between text-slate-600">
                <span>Diskon</span>
                <span class="font-semibold text-rose-600">- Rp {{ number_format($discount ?? 5000, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="flex justify-between text-slate-600">
                <span>Pajak (PPN 11%)</span>
                <span class="font-semibold text-slate-800">Rp {{ number_format($tax ?? 6820, 0, ',', '.') }}</span>
            </div>
            
            <div class="border-b border-dotted border-slate-300 my-1.5"></div>
            
            <div class="flex justify-between text-xs font-black text-slate-900">
                <span>GRAND TOTAL</span>
                <span class="text-sm font-extrabold text-indigo-650">Rp {{ number_format($grandTotal ?? 68820, 0, ',', '.') }}</span>
            </div>
            
            <div class="border-b border-dotted border-slate-300 my-1.5"></div>
            
            <div class="flex justify-between text-slate-600">
                <span>Tunai / Bayar</span>
                <span class="font-semibold text-slate-800">Rp {{ number_format($cashReceived ?? 100000, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-slate-600">
                <span>Kembalian</span>
                <span class="font-semibold text-emerald-700">Rp {{ number_format($change ?? 31180, 0, ',', '.') }}</span>
            </div>
        </div>
        
        <!-- Divider (Dashed) -->
        <div class="border-b border-dashed border-slate-300 my-4"></div>
        
        <!-- Footer / Greetings -->
        <div class="text-center space-y-1">
            <p class="font-bold text-[10px] text-slate-800">Terima Kasih</p>
            <p class="text-[9px] text-slate-500">Barang yang sudah dibeli</p>
            <p class="text-[9px] text-slate-500">tidak dapat ditukar/dikembalikan</p>
            
            <!-- Barcode representation for custom aesthetics -->
            <div class="pt-3 flex flex-col items-center justify-center opacity-70">
                <div class="h-6 w-36 bg-slate-900 flex items-center justify-between px-2 text-[6px] text-white tracking-widest font-mono">
                    ||||| | || |||| | ||| |||| |
                </div>
                <span class="text-[8px] text-slate-400 mt-1 font-mono tracking-wider">#{{ $invoice ?? 'TRX-20260701-085' }}</span>
            </div>
        </div>
        
    </div>

</body>
</html>
