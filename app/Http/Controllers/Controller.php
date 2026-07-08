<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected function rolePrefix(): string
    {
        $user = auth()->user();
        if ($user?->hasRole('Super Admin')) return 'admin';
        if ($user?->hasRole('Manager')) return 'manager';
        if ($user?->hasRole('Gudang')) return 'gudang';
        return 'app';
    }
}
