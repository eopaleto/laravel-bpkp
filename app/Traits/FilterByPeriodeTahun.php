<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Trait untuk memfilter data berdasarkan periode_tahun yang dipilih saat login
 * Secara otomatis akan memfilter query hanya menampilkan data sesuai periode tahun yang dipilih
 */
trait FilterByPeriodeTahun
{
    protected static function bootFilterByPeriodeTahun()
    {
        static::addGlobalScope(function (Builder $builder) {
            // Ambil dari session terlebih dahulu, fallback ke auth user
            $periodeTahun = session('periode_tahun');
            
            if (!$periodeTahun && auth()->check()) {
                $periodeTahun = auth()->user()->periode_tahun;
            }
            
            if ($periodeTahun) {
                $builder->where('periode_tahun', (int)$periodeTahun);
            }
        });
    }
}
