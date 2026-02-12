<?php

namespace App\Console\Commands;

use App\Models\LogBarangKeluar;
use App\Models\LogBarangMasuk;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class ResetKartuGudang extends Command
{
    protected $signature = 'kartu-gudang:reset {--periode= : Periode tahun (opsional, default dari session/auth)}';

    protected $description = 'Reset/kosongi data kartu gudang untuk periode tertentu';

    public function handle()
    {
        $periodeTahun = $this->option('periode') ?? (Auth::check() ? Auth::user()->periode_tahun : date('Y'));

        if (!$periodeTahun) {
            $this->error('Periode tahun tidak ditemukan!');
            return 1;
        }

        $this->info("Akan menghapus data kartu gudang untuk periode: {$periodeTahun}");

        if (!$this->confirm('Lanjutkan penghapusan?')) {
            $this->info('Dibatalkan.');
            return 0;
        }

        // Hitung jumlah data yang akan dihapus
        $keluarCount = LogBarangKeluar::where('periode_tahun', $periodeTahun)->count();
        $masukCount = LogBarangMasuk::where('periode_tahun', $periodeTahun)->count();

        $this->info("Data yang akan dihapus:");
        $this->info("- Log Barang Keluar: {$keluarCount} record");
        $this->info("- Log Barang Masuk: {$masukCount} record");

        // Hapus data
        LogBarangKeluar::where('periode_tahun', $periodeTahun)->delete();
        LogBarangMasuk::where('periode_tahun', $periodeTahun)->delete();

        $this->info('✓ Data kartu gudang berhasil dikosongkan!');
        return 0;
    }
}
