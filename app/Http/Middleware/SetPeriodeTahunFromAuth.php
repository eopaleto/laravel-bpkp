<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetPeriodeTahunFromAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jika session sudah ada periode_tahun, gunakan itu
        // Middleware ini hanya untuk fallback jika session belum ter-set
        if (!session()->has('periode_tahun') && auth()->check()) {
            $periodeTahun = auth()->user()->periode_tahun;
            if ($periodeTahun) {
                session()->put('periode_tahun', $periodeTahun);
            }
        }

        return $next($request);
    }
}
