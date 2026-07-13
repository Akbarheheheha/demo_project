<?php

declare(strict_types=1);

namespace App\Http\Controllers;

abstract class Controller
{
    protected function rolePrefix(): string
    {
        $user = auth()->user();
        if ($user?->hasRole('Super Admin')) return 'admin';
        if ($user?->hasRole('Manager')) return 'manager';
        if ($user?->hasRole('Gudang')) return 'gudang';
        if ($user?->hasRole('Tenant Owner')) return 'admin';
        return 'app';
    }
}
