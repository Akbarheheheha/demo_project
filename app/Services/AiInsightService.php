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
    protected string $apiKey;
    protected string $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
    }

    public function getBusinessInsights(): string
    {
        try {
            $metrics = $this->gatherMetrics();

            if (empty($this->apiKey)) {
                return "Konfigurasi API Key Gemini belum diset. Silakan tambahkan GEMINI_API_KEY di file .env.";
            }

            return $this->generateInsight($metrics);

        } catch (Exception $e) {
            Log::warning('AiInsightService Error: ' . $e->getMessage());
            return $this->fallbackInsight($e->getMessage());
        }
    }

    protected function gatherMetrics(): array
    {
        $totalOmset = (float) Transaction::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->where('status', 'success')
            ->sum('total_harga');

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

        $soldProductIds = TransactionDetail::join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->where('transactions.status', 'success')
            ->pluck('product_id')
            ->unique()
            ->toArray();

        $bottomProducts = Product::whereIn('id', $soldProductIds)
            ->select('products.id', 'products.name')
            ->selectRaw('(SELECT COALESCE(SUM(qty), 0) FROM transaction_details 
                        JOIN transactions ON transaction_details.transaction_id = transactions.id 
                        WHERE transactions.status = "success" AND transaction_details.product_id = products.id) as total_qty')
            ->orderBy('total_qty', 'asc')
            ->limit(3)
            ->get()
            ->map(fn($item) => "- {$item->name} (Terjual: {$item->total_qty} pcs)")
            ->implode("\n");

        $unsoldProducts = Product::whereNotIn('id', $soldProductIds)
            ->limit(max(0, 3 - Product::whereIn('id', $soldProductIds)->count()))
            ->get()
            ->map(fn($item) => "- {$item->name} (Belum terjual)")
            ->implode("\n");

        return [
            'total_omset' => $totalOmset,
            'top_3_products' => $topProducts ?: "- Belum ada penjualan",
            'bottom_3_products' => $bottomProducts ?: $unsoldProducts ?: "- Tidak ada data",
        ];
    }

    protected function generateInsight(array $metrics): string
    {
        $prompt = "Berikut adalah metrik penjualan UMKM saya:\n\n" .
                  "1. Total Omset Bulan Ini: Rp " . number_format($metrics['total_omset'], 0, ',', '.') . "\n\n" .
                  "2. 3 Produk Terlaris:\n{$metrics['top_3_products']}\n\n" .
                  "3. 3 Produk Kurang Laku:\n{$metrics['bottom_3_products']}\n\n" .
                  "Sebagai Konsultan Bisnis UMKM, berikan insight singkat (maks 3-4 kalimat) dengan saran operasional praktis.";

        $systemInstruction = "Anda adalah Konsultan Bisnis UMKM yang ahli menganalisis data penjualan. " .
                             "Berikan insight & rekomendasi dalam 1 paragraf singkat (3-4 kalimat), gunakan bahasa Indonesia yang jelas dan langsung ke solusi.";

        try {
            $response = Http::timeout(8)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($this->apiUrl . '?key=' . $this->apiKey, [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]]
                    ],
                    'systemInstruction' => [
                        'parts' => [['text' => $systemInstruction]]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => 350
                    ]
                ]);

            if ($response->failed()) {
                Log::error('Gemini API Error: ' . $response->body());
                return $this->fallbackInsight('API request failed');
            }

            $result = $response->json();
            $insight = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;

            return $insight ? trim($insight) : $this->fallbackInsight('No response');

        } catch (Exception $e) {
            return $this->fallbackInsight($e->getMessage());
        }
    }

    protected function fallbackInsight(string $reason): string
    {
        return "Koneksi AI Advisor sedang bermasalah ($reason). Silakan cek koneksi internet atau coba lagi nanti.";
    }
}
