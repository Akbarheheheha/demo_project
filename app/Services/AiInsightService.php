<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiInsightService
{
    private const TIMEOUT_SECONDS = 15;
    private const RETRY_COUNT = 1;
    private const RETRY_DELAY_MS = 500;

    /**
     * Get business insights from Gemini API based on current store metrics.
     *
     * @param  array|null  $additionalData Optional extra context (dailyTrend, category breakdown, etc.)
     * @return string
     */
    public function getBusinessInsights(?array $additionalData = []): string
    {
        $metrics = $this->gatherMetrics();

        $dailyTrend = $additionalData['daily_trend'] ?? $this->gatherDailyTrend();
        $metrics['daily_trend'] = $dailyTrend;

        $geminiInsight = $this->tryGeminiApi($metrics);

        if ($geminiInsight !== null) {
            return $geminiInsight;
        }

        return $this->generateLocalInsight($metrics);
    }

    private function gatherMetrics(): array
    {
        $totalOmset = (float) Transaction::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->where('status', 'success')
            ->sum('total_harga');

        $transactionCount = Transaction::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->where('status', 'success')
            ->count();

        $avgTransactionValue = $transactionCount > 0
            ? round($totalOmset / $transactionCount, 2)
            : 0;

        $topProducts = TransactionDetail::join('products', 'transaction_details.product_id', '=', 'products.id')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->where('transactions.status', 'success')
            ->selectRaw('products.name, products.stock, SUM(transaction_details.qty) as total_qty')
            ->groupBy('products.id', 'products.name', 'products.stock')
            ->orderByDesc('total_qty')
            ->limit(3)
            ->get()
            ->map(fn($item) => sprintf(
                "- %s (Terjual: %d pcs, Stok: %d)",
                $item->name,
                $item->total_qty,
                $item->stock
            ))
            ->implode("\n");

        $bottomProducts = Product::select('products.name', 'products.stock')
            ->selectRaw('COALESCE(SUM(transaction_details.qty), 0) as total_qty')
            ->leftJoin('transaction_details', 'products.id', '=', 'transaction_details.product_id')
            ->leftJoin('transactions', function ($join) {
                $join->on('transaction_details.transaction_id', '=', 'transactions.id')
                     ->where('transactions.status', '=', 'success');
            })
            ->groupBy('products.id', 'products.name', 'products.stock')
            ->orderBy('total_qty')
            ->limit(3)
            ->get()
            ->map(fn($item) => sprintf(
                "- %s (Terjual: %d pcs, Stok: %d)",
                $item->name,
                $item->total_qty,
                $item->stock
            ))
            ->implode("\n");

        $totalProducts = Product::count();
        $lowStockProducts = Product::whereColumn('stock', '<=', 'min_stock')
            ->orWhere('stock', '<=', 5)
            ->count();

        return [
            'total_omset' => $totalOmset,
            'transaction_count' => $transactionCount,
            'avg_transaction_value' => $avgTransactionValue,
            'top_products' => $topProducts,
            'bottom_products' => $bottomProducts,
            'total_products' => $totalProducts,
            'low_stock_count' => $lowStockProducts,
            'daily_trend' => [],
        ];
    }

    private function gatherDailyTrend(): array
    {
        $sevenDaysAgo = Carbon::now()->subDays(6)->startOfDay();

        $salesData = Transaction::where('status', 'success')
            ->where('created_at', '>=', $sevenDaysAgo)
            ->selectRaw('DATE(created_at) as date, SUM(total_harga) as total')
            ->groupBy('date')
            ->pluck('total', 'date')
            ->toArray();

        $trend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateString = $date->toDateString();
            $dayName = $date->translatedFormat('l');
            $total = isset($salesData[$dateString]) ? (float) $salesData[$dateString] : 0;
            $trend[] = [
                'day' => $dayName,
                'date' => $dateString,
                'total' => $total,
            ];
        }

        return $trend;
    }

    private function buildPrompt(array $metrics): string
    {
        $formattedOmset = 'Rp ' . number_format($metrics['total_omset'], 0, ',', '.');
        $formattedAvg = 'Rp ' . number_format($metrics['avg_transaction_value'], 0, ',', '.');

        $dailyHistory = '';
        if (!empty($metrics['daily_trend'])) {
            $lines = [];
            foreach ($metrics['daily_trend'] as $day) {
                $nominal = 'Rp ' . number_format($day['total'], 0, ',', '.');
                $lines[] = "  {$day['day']} ({$day['date']}): {$nominal}";
            }
            $dailyHistory = implode("\n", $lines);
        }

        return
            "Berikut adalah data analitik toko UMKM untuk bulan ini:\n" .
            "\n" .
            "--- RINGKASAN KEUANGAN ---\n" .
            "Total Omset Bulan Ini: {$formattedOmset}\n" .
            "Total Transaksi: {$metrics['transaction_count']} transaksi\n" .
            "Rata-rata Nilai Transaksi: {$formattedAvg}\n" .
            "\n" .
            "--- TREN PENJUALAN 7 HARI TERAKHIR ---\n" .
            "({$dailyHistory})\n" .
            "\n" .
            "--- PRODUK ---\n" .
            "3 Produk Paling Laris (Top 3):\n" .
            ($metrics['top_products'] ?: "  - Belum ada data\n") .
            "\n" .
            "3 Produk Kurang Laku (Bottom 3):\n" .
            ($metrics['bottom_products'] ?: "  - Belum ada data\n") .
            "\n" .
            "Total Produk Tersedia: {$metrics['total_products']} produk\n" .
            "Jumlah Produk dengan Stok Menipis: {$metrics['low_stock_count']} produk\n" .
            "\n" .
            "Berdasarkan data di atas, berikan insight bisnis singkat (maks 4 kalimat) dalam bahasa Indonesia yang mencakup:\n" .
            "1. Analisis singkat tren penjualan terbaru\n" .
            "2. Rekomendasi operasional konkret berdasarkan data produk\n" .
            "3. Saran tindakan untuk produk stok menipis\n" .
            "4. Satu pertanyaan investigatif yang bisa membantu pemilik toko menggali lebih dalam\n" .
            "\n" .
            "Gunakan format alami, jangan gunakan bullet point atau penomoran dalam jawaban.";
    }

    private function tryGeminiApi(array $metrics): ?string
    {
        $apiKey = config('services.gemini.key');
        if (empty($apiKey) || $apiKey === 'your_gemini_api_key_here') {
            Log::info('AiInsightService: Gemini API key not configured, using local fallback.');
            return null;
        }

        $prompt = $this->buildPrompt($metrics);

        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-lite:generateContent';

        $attempts = self::RETRY_COUNT + 1;
        for ($attempt = 1; $attempt <= $attempts; $attempt++) {
            try {
                $response = Http::timeout(self::TIMEOUT_SECONDS)
                    ->withOptions(['connect_timeout' => 5])
                    ->withHeaders([
                        'Content-Type' => 'application/json',
                        'x-goog-api-key' => $apiKey,
                    ])
                    ->post($url, [
                        'contents' => [
                            ['parts' => [['text' => $prompt]]],
                        ],
                        'systemInstruction' => [
                            'parts' => [[
                                'text' => implode(' ', [
                                    'Anda adalah Konsultan Bisnis UMKM profesional di Indonesia.',
                                    'Analisis data metrik penjualan toko dan berikan rekomendasi',
                                    'operasional konkret dalam 1 paragraf singkat (maks 4 kalimat).',
                                    'Gunakan bahasa Indonesia santun dan mudah dipahami.',
                                    'Jangan gunakan format daftar atau bullet points dalam jawaban.',
                                ]),
                            ]],
                        ],
                        'generationConfig' => [
                            'temperature' => 0.7,
                            'maxOutputTokens' => 350,
                        ],
                    ]);

                if ($response->successful()) {
                    $result = $response->json();
                    $insight = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;
                    if (!empty($insight)) {
                        return trim($insight);
                    }
                }

                $status = $response->status();

                if ($status === 429) {
                    if ($attempt < $attempts) {
                        usleep(self::RETRY_DELAY_MS * 1000);
                        continue;
                    }
                    Log::warning('AiInsightService: Gemini rate limited after all retries.');
                    return null;
                }

                Log::error("AiInsightService: Gemini API HTTP {$status}: " . substr($response->body(), 0, 500));

                return implode(' ', [
                    'Asisten AI sedang tidak dapat dihubungi saat ini.',
                    'Silakan coba beberapa saat lagi.',
                    'Data penjualan tetap bisa Anda lihat langsung di grafik dan ringkasan di atas.',
                ]);
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                Log::warning("AiInsightService: Gemini connection timeout (attempt {$attempt}): " . $e->getMessage());
                if ($attempt < $attempts) {
                    usleep(self::RETRY_DELAY_MS * 1000);
                    continue;
                }
                return null;
            } catch (\Exception $e) {
                Log::warning("AiInsightService: Gemini unexpected error (attempt {$attempt}): " . $e->getMessage());
                if ($attempt < $attempts) {
                    usleep(self::RETRY_DELAY_MS * 1000);
                    continue;
                }
                return null;
            }
        }

        return null;
    }

    private function generateLocalInsight(array $metrics): string
    {
        $totalOmset = $metrics['total_omset'];
        $topProducts = $metrics['top_products'];
        $bottomProducts = $metrics['bottom_products'];
        $lowStockCount = $metrics['low_stock_count'];
        $dailyTrend = $metrics['daily_trend'] ?? [];

        $parts = [];

        if ($totalOmset > 0) {
            $formattedOmset = 'Rp ' . number_format($totalOmset, 0, ',', '.');
            $parts[] = "Omset bulan ini mencapai {$formattedOmset} dari {$metrics['transaction_count']} transaksi.";

            if ($totalOmset > 5000000) {
                $parts[] = 'Kinerja cukup baik, pertahankan tren ini dengan menjaga kualitas layanan.';
            } elseif ($totalOmset > 1000000) {
                $parts[] = 'Mulai terlihat pertumbuhan, coba tingkatkan promosi produk via media sosial.';
            } else {
                $parts[] = 'Masih perlu dorongan lebih, fokus pada pemasaran produk terlaris.';
            }
        } else {
            $parts[] = 'Belum ada transaksi bulan ini. Coba mulai dengan promosi ke pelanggan terdekat.';
        }

        if (!empty($topProducts)) {
            $parts[] = 'Produk terlaris bisa dijadikan andalan untuk promo bundle agar meningkatkan nilai transaksi.';
        }

        if (!empty($bottomProducts)) {
            $parts[] = 'Produk yang kurang laku perlu dievaluasi ulang atau dikenakan diskon untuk mengurangi stok.';
        }

        if ($lowStockCount > 0) {
            $parts[] = "Ada {$lowStockCount} produk dengan stok menipis. Segera lakukan restok untuk menghindari kehabisan stok.";
        }

        if (!empty($dailyTrend)) {
            $maxDay = collect($dailyTrend)->sortByDesc('total')->first();
            $minDay = collect($dailyTrend)->where('total', '>', 0)->sortBy('total')->first();

            if ($maxDay && $maxDay['total'] > 0) {
                $formattedMax = 'Rp ' . number_format($maxDay['total'], 0, ',', '.');
                $parts[] = "Hari dengan penjualan tertinggi 7 hari terakhir adalah {$maxDay['day']} ({$formattedMax}).";
            }

            if ($minDay && $minDay['total'] > 0) {
                $formattedMin = 'Rp ' . number_format($minDay['total'], 0, ',', '.');
                $parts[] = "Hari dengan penjualan terendah adalah {$minDay['day']} ({$formattedMin}).";
            }
        }

        $parts[] = 'Pantau terus stok dan sesuaikan strategi setiap minggu.';

        return implode(' ', $parts);
    }
}
