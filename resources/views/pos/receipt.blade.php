<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Belanja - #{{ $invoice ?? 'TRX-20260701-999' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @php
        $isA4 = $receiptSize === 'A4';
        $is58 = $receiptSize === '58mm';
        $is80 = $receiptSize === '80mm' || (!$isA4 && !$is58);

        $printPadding = $is58 ? '2mm' : ($isA4 ? '8mm' : '4mm');
        $screenPadding = $is58 ? 'p-3' : ($isA4 ? 'p-6' : 'p-4');
        $printFontSize = $is58 ? '10px' : ($isA4 ? '14px' : '11px');
        $screenFontSize = $is58 ? 'text-[10px]' : ($isA4 ? 'text-sm' : 'text-[11px]');
        $headerFontSize = $is58 ? 'text-xs' : ($isA4 ? 'text-base' : 'text-sm');
    @endphp
    <style>
        :root {
            --receipt-width: {{ $receiptSize }};
            --print-padding: {{ $printPadding }};
        }

        @media print {
            body {
                background-color: #ffffff;
                color: #000000;
                margin: 0;
                padding: 0;
                font-size: {{ $printFontSize }};
            }
            .no-print {
                display: none !important;
            }
            header, footer, aside, nav, #sidebar, .sidebar-animate-bg, #spa-progressbar {
                display: none !important;
            }
            @page {
                size: {{ $receiptSize }} auto;
                margin: 0;
            }
            .print-container {
                width: var(--receipt-width);
                max-width: var(--receipt-width);
                margin: 0;
                padding: var(--print-padding);
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
            }
            .print-container .text-slate-300,
            .print-container .text-slate-400,
            .print-container .text-slate-500,
            .print-container .text-slate-600,
            .print-container .text-slate-700 {
                color: #000 !important;
            }
            .print-container .border-slate-200,
            .print-container .border-slate-100 {
                border-color: #ddd !important;
            }
        }

        body {
            background-color: #f1f5f9;
        }
    </style>
</head>
<body class="font-mono text-slate-800 antialiased min-h-screen flex flex-col items-center justify-start p-4 sm:p-6 {{ $screenFontSize }}">

    <div class="no-print w-full" style="max-width: {{ $receiptSize }}; margin-bottom: 1rem; display: flex; gap: 0.5rem;">
        <button onclick="window.history.back()" class="flex-1 py-2 px-3 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-medium rounded-xl text-xs flex items-center justify-center gap-1.5 transition-all shadow-sm">
            &larr; Kembali
        </button>
        <button onclick="window.print()" class="flex-[2] py-2 px-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs flex items-center justify-center gap-1.5 transition-all shadow-md shadow-indigo-650/15">
            Cetak Struk (Print)
        </button>
    </div>

    <div class="print-container bg-white {{ $screenPadding }} border border-slate-200 shadow-sm rounded-2xl flex flex-col leading-relaxed"
         style="width: {{ $receiptSize }}; max-width: {{ $receiptSize }};">

        <div class="text-center space-y-1 mb-3">
            <h1 class="{{ $headerFontSize }} font-extrabold uppercase tracking-wide text-slate-900">{{ $shopName }}</h1>
            @if($shopAddress)<p class="text-[10px] text-slate-500">{{ $shopAddress }}</p>@endif
            @if($shopPhone)<p class="text-[9px] text-slate-400">Telp: {{ $shopPhone }}</p>@endif
        </div>

        <div class="border-b border-dashed border-slate-300 my-1.5"></div>

        <div class="space-y-1 text-slate-600 my-1">
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
            @if(!empty($customer_name))
            <div class="flex justify-between">
                <span>Pelanggan:</span>
                <span class="font-bold text-slate-800">{{ $customer_name }}</span>
            </div>
            @endif
        </div>

        <div class="border-b border-dashed border-slate-300 my-1.5"></div>

        <div class="grid grid-cols-12 gap-1 font-bold text-slate-700 mb-1 uppercase">
            <div class="col-span-6">Nama Barang</div>
            <div class="col-span-2 text-center">Qty</div>
            <div class="col-span-4 text-right">Subtotal</div>
        </div>

        @if($items)
        <div class="space-y-1.5">
            @foreach($items as $item)
                <div>
                    <div class="text-slate-800 font-bold">{{ $item['name'] }}</div>
                    <div class="grid grid-cols-12 gap-1 text-slate-650">
                        <div class="col-span-6 pl-1.5 text-slate-500">
                            {{ number_format($item['qty'], 0) }} x {{ number_format($item['price'], 0, ',', '.') }}
                        </div>
                        <div class="col-span-6 text-right font-semibold text-slate-800">
                            Rp {{ number_format($item['total'], 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @endif

        <div class="border-b border-dashed border-slate-300 my-2"></div>

        @php
            $taxAmount = $tax ?? 0;
            $discountAmount = $discount ?? 0;
            $effectiveSubtotal = $subtotal - $discountAmount;
            $taxPercentage = $effectiveSubtotal > 0 ? round(($taxAmount * 100) / $effectiveSubtotal) : 0;
            $calculatedGrandTotal = $grandTotal ?? ($subtotal - $discountAmount + $taxAmount);
            $calculatedChange = $change ?? max(0, $cashReceived - $calculatedGrandTotal);
        @endphp
        <div class="space-y-1">
            <div class="flex justify-between text-slate-600">
                <span>Subtotal</span>
                <span class="font-semibold text-slate-800">Rp {{ number_format($subtotal ?? 67000, 0, ',', '.') }}</span>
            </div>
            @if($discountAmount > 0)
            <div class="flex justify-between text-slate-600">
                <span>Diskon</span>
                <span class="font-semibold text-rose-600">- Rp {{ number_format($discountAmount, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="flex justify-between text-slate-600">
                <span>Pajak (PPN {{ $taxPercentage }}%)</span>
                <span class="font-semibold text-slate-800">Rp {{ number_format($taxAmount, 0, ',', '.') }}</span>
            </div>

            <div class="border-b border-dotted border-slate-300 my-1"></div>

            <div class="flex justify-between font-black text-slate-900">
                <span>GRAND TOTAL</span>
                <span class="font-extrabold text-indigo-650">Rp {{ number_format($calculatedGrandTotal, 0, ',', '.') }}</span>
            </div>

            <div class="border-b border-dotted border-slate-300 my-1"></div>

            @if($paymentMethod === 'Tunai')
            <div class="flex justify-between text-slate-600">
                <span>Tunai / Bayar</span>
                <span class="font-semibold text-slate-800">Rp {{ number_format($cashReceived ?? 100000, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-slate-600">
                <span>Kembalian</span>
                <span class="font-semibold text-emerald-700">Rp {{ number_format($calculatedChange, 0, ',', '.') }}</span>
            </div>
            @else
            <div class="flex justify-between text-slate-600">
                <span>Dibayar</span>
                <span class="font-semibold text-slate-800">Rp {{ number_format($calculatedGrandTotal, 0, ',', '.') }}</span>
            </div>
            @endif
        </div>

        <div class="border-b border-dashed border-slate-300 my-3"></div>

        <div class="text-center space-y-1">
            <p class="font-bold text-slate-800">Terima Kasih</p>
            <p class="text-slate-500">Barang yang sudah dibeli</p>
            <p class="text-slate-500">tidak dapat ditukar/dikembalikan</p>

            <div class="pt-2 flex flex-col items-center justify-center opacity-70">
                <div class="h-6 w-36 bg-slate-900 flex items-center justify-between px-2 text-white tracking-widest font-mono">
                    ||||| | || |||| | ||| |||| |
                </div>
                <span class="text-slate-400 mt-1 font-mono tracking-wider">#{{ $invoice ?? 'TRX-20260701-085' }}</span>
            </div>
        </div>

    </div>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            if (window.self !== window.top) return;
            setTimeout(() => {
                window.print();
            }, 300);
        });

        window.onafterprint = function() {
            window.close();
        };

        document.addEventListener('keydown', function(event) {
            if (event.key === 'x' || event.key === 'X') {
                if (window.opener || window.history.length === 1) {
                    window.close();
                } else {
                    window.history.back();
                }
            }
        });
    </script>
</body>
</html>
