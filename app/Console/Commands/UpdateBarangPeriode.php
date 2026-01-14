<?php

namespace App\Console\Commands;

use App\Models\Barang;
use Illuminate\Console\Command;

class UpdateBarangPeriode extends Command
{
    protected $signature = 'barang:set-periode {tahun=2026}';
    protected $description = 'Set periode_tahun untuk semua barang yang NULL';

    public function handle(): int
    {
        $tahun = (int) $this->argument('tahun');
        
        $updated = Barang::whereNull('periode_tahun')
            ->update(['periode_tahun' => $tahun]);

        $this->info("✅ Updated {$updated} barang dengan periode_tahun = {$tahun}");
        
        // Tampilkan total barang per periode
        $counts = Barang::select('periode_tahun')
            ->groupBy('periode_tahun')
            ->withCount('*')
            ->get();
            
        foreach ($counts as $count) {
            $this->line("Periode {$count->periode_tahun}: {$count->count} barang");
        }

        return 0;
    }
}
