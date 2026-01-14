<?php

/**
 * Get the selected periode tahun from session
 * 
 * @return int|null
 */
if (!function_exists('getPeriodeTahun')) {
    function getPeriodeTahun(): ?int
    {
        $periode = session('periode_tahun');
        
        // Fallback ke auth user jika session kosong
        if (!$periode && auth()->check()) {
            $periode = auth()->user()->periode_tahun;
        }
        
        return $periode ? (int)$periode : null;
    }
}

/**
 * Check if user has selected periode tahun
 * 
 * @return bool
 */
if (!function_exists('hasPeriodeTahun')) {
    function hasPeriodeTahun(): bool
    {
        return (bool) getPeriodeTahun();
    }
}

/**
 * Debug periode tahun (untuk testing)
 */
if (!function_exists('debugPeriodeTahun')) {
    function debugPeriodeTahun(): array
    {
        return [
            'session' => session('periode_tahun'),
            'auth_user' => auth()->check() ? auth()->user()->periode_tahun : null,
            'final_value' => getPeriodeTahun(),
        ];
    }
}
