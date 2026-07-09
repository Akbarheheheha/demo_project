<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

final class CheckStoreStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\User|null $user */
        $user = $request->user();

        if ($user !== null && $user->store_id !== null) {
            $store = $user->store;

            if ($store === null || $store->status !== 'active') {
                Auth::logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors([
                    'email' => 'Akses toko Anda telah ditangguhkan. Silakan hubungi administrator.',
                ]);
            }
        }

        return $next($request);
    }
}
