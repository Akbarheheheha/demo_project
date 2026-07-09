<?php

declare(strict_types=1);

namespace App\Models\Traits;

use App\Models\Scopes\TenantScope;
use App\Services\TenantManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(app(TenantScope::class));

        static::creating(static function (Model $model): void {
            $storeId = app(TenantManager::class)->getStoreId();

            if ($storeId !== null) {
                $model->setAttribute($model->getStoreIdColumn(), $storeId);
            }
        });
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Store::class);
    }

    protected function getStoreIdColumn(): string
    {
        return 'store_id';
    }

    public function getQualifiedStoreIdColumn(): string
    {
        return $this->qualifyColumn($this->getStoreIdColumn());
    }
}