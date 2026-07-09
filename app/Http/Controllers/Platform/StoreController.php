<?php

declare(strict_types=1);

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class StoreController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $stores = Store::withoutTenancy()
            ->withCount('users')
            ->orderBy('created_at', 'desc')
            ->paginate($request->integer('per_page', 15));

        $storeIds = $stores->pluck('id');

        $revenues = Transaction::withoutTenancy()
            ->whereIn('store_id', $storeIds)
            ->where('status', 'success')
            ->selectRaw('store_id, SUM(total_harga) as total_revenue, COUNT(*) as transaction_count')
            ->groupBy('store_id')
            ->get()
            ->keyBy('store_id');

        $stores->getCollection()->transform(function (Store $store) use ($revenues): Store {
            $store->total_revenue = $revenues->get($store->id)?->total_revenue ?? 0;
            $store->transaction_count = $revenues->get($store->id)?->transaction_count ?? 0;
            return $store;
        });

        return response()->json([
            'message' => 'Daftar semua toko.',
            'data' => $stores,
        ]);
    }

    public function toggleStatus(string $storeId): JsonResponse
    {
        $store = Store::withoutTenancy()->findOrFail($storeId);

        $store->status = $store->status === 'active' ? 'suspended' : 'active';
        $store->save();

        return response()->json([
            'message' => "Status toko berhasil diubah menjadi {$store->status}.",
            'data' => $store->only('id', 'name', 'status'),
        ]);
    }
}
