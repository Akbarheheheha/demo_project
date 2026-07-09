<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class SetTenantStoreId
{
    public function __construct(
        private readonly TenantManager $tenantManager,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\User|null $user */
        $user = $request->user();

        if ($user !== null && $user->store_id !== null) {
            $this->tenantManager->setStoreId((int) $user->store_id);
        }

        return $next($request);
    }
}