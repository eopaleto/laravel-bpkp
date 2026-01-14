<?php

namespace App\Console\Commands;

use App\Models\Barang;
use Illuminate\Console\Command;

class UpdateBarangPeriodeTahun extends Command
{
    protected $signature = 'barang:update-periode-tahun {--tahun=2026}';

    protected $description = 'Update semua barang yang periode_tahunnya NULL menjadi tahun yang ditentukan';

    public function handle(): int
    {
        $tahun = (int) $this->option('tahun');

        $updated = Barang::whereNull('periode_tahun')
            ->update(['periode_tahun' => $tahun]);

        $this->info("✅ Berhasil mengupdate {$updated} barang dengan periode_tahun = {$tahun}");

        return 0;
    }
}
