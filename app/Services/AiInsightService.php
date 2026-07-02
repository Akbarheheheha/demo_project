<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class AiInsightService
{
    /**
     * Get business insights from Gemini API based on current store metrics.
     *
     * @return string
     */
    public function getBusinessInsights(): string
    {
        try {
            // 1. Gather Metrics from Database
            // a. Total Omset Bulan Ini (status success)
            $totalOmset = (float) Transaction::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->where('status', 'success')
                ->sum('total_harga');

            // b. Top 3 Best-Selling Items (status success)
            $topProducts = TransactionDetail::join('products', 'transaction_details.product_id', '=', 'products.id')
                ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
                ->where('transactions.status', 'success')
                ->selectRaw('products.name, SUM(transaction_details.qty) as total_qty')
                ->groupBy('products.id', 'products.name')
                ->orderByDesc('total_qty')
                ->limit(3)
                ->get()
                ->map(fn($item) => "- {$item->name} (Terjual: {$item->total_qty} pcs)")
                ->implode("\n");

            // c. Bottom 3 Least-Selling Items
            $bottomProducts = Product::select('products.name')
                ->selectRaw('COALESCE(SUM(transaction_details.qty), 0) as total_qty')
                ->leftJoin('transaction_details', function($join) {
                    $join->on('products.id', '=', 'transaction_details.product_id')
                         ->join('transactions', function($joinTrans) {
                             $joinTrans->on('transaction_details.transaction_id', '=', 'transactions.id')
                                       ->where('transactions.status', 'success');
                         });
                })
                ->groupBy('products.id', 'products.name')
                ->orderBy('total_qty', 'asc')
                ->limit(3)
                ->get()
                ->map(fn($item) => "- {$item->name} (Terjual: {$item->total_qty} pcs)")
                ->implode("\n");

            // Prepare prompt string
            $formattedOmset = 'Rp ' . number_format($totalOmset, 0, ',', '.');
            $prompt = "Berikut adalah metrik penjualan toko UMKM kami untuk bulan ini:\n\n" .
                      "1. **Total Omset Bulan Ini**: {$formattedOmset}\n" .
                      "2. **3 Produk Paling Laris (Top 3)**:\n" . ($topProducts ?: "- Tidak ada data penjualan\n") . "\n" .
                      "3. **3 Produk Paling Kurang Laku (Bottom 3)**:\n" . ($bottomProducts ?: "- Tidak ada data produk\n") . "\n" .
                      "Berikan insight bisnis singkat, padat, dan langsung ke solusi operasional.";

            // 2. HTTP Call to Gemini API
            $apiKey = config('services.gemini.key');

            if (empty($apiKey) || $apiKey === 'your_gemini_api_key_here') {
                return "Konfigurasi API Key Gemini belum diset di file .env. Silakan atur GEMINI_API_KEY untuk melihat insight bisnis otomatis.";
            }

            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-lite:generateContent?key={$apiKey}";

            // System prompt
            $systemInstruction = "Anda adalah Konsultan Bisnis UMKM profesional yang cerdas, praktis, dan suportif. " .
                                 "Analisis data metrik penjualan toko dan berikan rekomendasi operasional konkret dalam 1 paragraf singkat saja (maksimal 3 sampai 4 kalimat). " .
                                 "Gunakan bahasa Indonesia yang santun dan mudah dipahami, hindari penjelasan teoretis yang bertele-tele.";

            $response = Http::timeout(8) // Set 8 seconds timeout
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'systemInstruction' => [
                        'parts' => [
                            ['text' => $systemInstruction]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => 350
                    ]
                ]);

            if ($response->failed()) {
                Log::error('Gemini API Error: ' . $response->body());
                return "Maaf, sistem gagal menghubungi AI Advisor saat ini. Coba segarkan halaman beberapa saat lagi.";
            }

            $result = $response->json();
            $insight = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (empty($insight)) {
                return "Tidak ada insight yang dapat dihasilkan dari data saat ini.";
            }

            return trim($insight);

        } catch (Exception $e) {
            Log::warning('AiInsightService Error: ' . $e->getMessage());
            return "Koneksi ke server AI Insight mengalami gangguan (timeout). Silakan periksa koneksi internet Anda atau coba kembali nanti.";
        }
    }
}
