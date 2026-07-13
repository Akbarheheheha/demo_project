<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

final class TenantRegistrationService
{
    public function register(array $data): TenantRegistrationResult
    {
        return DB::transaction(function () use ($data): TenantRegistrationResult {
            $store = Store::create([
                'uuid' => (string) Str::uuid(),
                'name' => $data['store_name'],
                'slug' => $this->generateUniqueSlug($data['store_name']),
                'email' => $data['store_email'] ?? null,
                'phone' => $data['store_phone'] ?? null,
                'subscription_plan' => 'free',
                'status' => 'active',
            ]);

            $user = User::create([
                'store_id' => $store->id,
                'name' => $data['owner_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $user->assignRole('Tenant Owner');

            return new TenantRegistrationResult($store, $user);
        });
    }

    private function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $original = $slug;
        $counter = 1;

        while (Store::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $counter++;
        }

        return $slug;
    }
}
