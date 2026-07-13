<?php

declare(strict_types=1);

namespace App\Services;

final class TenantManager
{
    private ?int $storeId = null;

    public function setStoreId(?int $storeId): void
    {
        $this->storeId = $storeId;
    }

    public function getStoreId(): ?int
    {
        return $this->storeId;
    }

    public function hasStoreId(): bool
    {
        return $this->storeId !== null;
    }

    public function forget(): void
    {
        $this->storeId = null;
    }
}