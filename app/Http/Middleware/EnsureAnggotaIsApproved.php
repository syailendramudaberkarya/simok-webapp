<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAnggotaIsApproved
{
    /**
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isAnggota()) {
            if ($user->anggota && $user->anggota->isPending()) {
                if ($request->routeIs('anggota.kartu')) {
                    session()->flash('error', 'Keanggotaan Anda masih menunggu persetujuan.');
                    return redirect()->route('anggota.profil');
                }
            }
        }

        return $next($request);
    }
}
