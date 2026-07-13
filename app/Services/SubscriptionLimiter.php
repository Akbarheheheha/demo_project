<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Store;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

final class SubscriptionLimiter
{
    private const array LIMITS = [
        'products' => [
            'free' => 50,
            'pro' => null,
        ],
        'kasir' => [
            'free' => 2,
            'pro' => null,
        ],
    ];

    public function __construct(
        private readonly TenantManager $tenantManager,
    ) {}

    public function canCreate(string $resource): bool
    {
        return $this->remaining($resource) > 0;
    }

    public function remaining(string $resource): int
    {
        $limit = $this->resolveLimit($resource);

        if ($limit === null) {
            return PHP_INT_MAX;
        }

        return max(0, $limit - $this->countCurrent($resource));
    }

    public function limit(string $resource): ?int
    {
        return $this->resolveLimit($resource);
    }

    public function ensure(string $resource, ?string $message = null): void
    {
        if ($this->canCreate($resource)) {
            return;
        }

        $plan = $this->resolvePlan();
        $limit = $this->resolveLimit($resource);
        $label = $this->resourceLabel($resource);

        throw new HttpResponseException(
            response()->json([
                'message' => $message ?? sprintf(
                    'Batas maksimal %d %s untuk paket %s telah tercapai. Silakan upgrade paket langganan Anda.',
                    $limit,
                    $label,
                    ucfirst($plan),
                ),
            ], 403)
        );
    }

    private function resolveLimit(string $resource): ?int
    {
        $plan = $this->resolvePlan();

        return self::LIMITS[$resource][$plan] ?? null;
    }

    private function resolvePlan(): string
    {
        $storeId = $this->tenantManager->getStoreId();

        if ($storeId === null) {
            return 'free';
        }

        return Store::where('id', $storeId)->value('subscription_plan') ?? 'free';
    }

    private function countCurrent(string $resource): int
    {
        return match ($resource) {
            'products' => \App\Models\Product::count(),
            'kasir' => \App\Models\User::count(),
            default => 0,
        };
    }

    private function resourceLabel(string $resource): string
    {
        return match ($resource) {
            'products' => 'produk',
            'kasir' => 'pengguna kasir',
            default => $resource,
        };
    }
}
