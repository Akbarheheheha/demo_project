<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Store;
use App\Models\User;

final readonly class TenantRegistrationResult
{
    public function __construct(
        public Store $store,
        public User $user,
    ) {}
}
