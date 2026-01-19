<?php

namespace App\Observers;

use App\Models\LogBarangKeluar;
use App\Models\KartuGudang;

class LogBarangKeluarObserver
{
    /**
     * Handle the LogBarangKeluar "created" event.
     */
    public function created(LogBarangKeluar $logBarangKeluar): void
    {
        KartuGudang::create([
            'kode_barang' => $logBarangKeluar->kode_barang,
            'nama_barang' => $logBarangKeluar->barang?->nama ?? $logBarangKeluar->kode_barang,
            'unit_kerja_id' => $logBarangKeluar->unit_kerja_id ?? 1,
            'tanggal_keluar' => $logBarangKeluar->created_at,
            'jumlah_keluar' => $logBarangKeluar->jumlah,
            'sisa_stok' => $logBarangKeluar->sisa_stok_saat_itu,
            'jenis' => 'barang_keluar',
            'keterangan' => $logBarangKeluar->keterangan,
            'periode_tahun' => $logBarangKeluar->periode_tahun,
        ]);
    }

    /**
     * Handle the LogBarangKeluar "updated" event.
     */
    public function updated(LogBarangKeluar $logBarangKeluar): void
    {
        // Find and update the corresponding KartuGudang record
        KartuGudang::where('kode_barang', $logBarangKeluar->kode_barang)
            ->where('jenis', 'barang_keluar')
            ->where('created_at', $logBarangKeluar->created_at)
            ->update([
                'nama_barang' => $logBarangKeluar->barang?->nama ?? $logBarangKeluar->kode_barang,
                'unit_kerja_id' => $logBarangKeluar->unit_kerja_id ?? 1,
                'tanggal_keluar' => $logBarangKeluar->created_at,
                'jumlah_keluar' => $logBarangKeluar->jumlah,
                'sisa_stok' => $logBarangKeluar->sisa_stok_saat_itu,
                'keterangan' => $logBarangKeluar->keterangan,
                'periode_tahun' => $logBarangKeluar->periode_tahun,
            ]);
    }

    /**
     * Handle the LogBarangKeluar "deleted" event.
     */
    public function deleted(LogBarangKeluar $logBarangKeluar): void
    {
        KartuGudang::where('kode_barang', $logBarangKeluar->kode_barang)
            ->where('jenis', 'barang_keluar')
            ->where('created_at', $logBarangKeluar->created_at)
            ->delete();
    }
}
