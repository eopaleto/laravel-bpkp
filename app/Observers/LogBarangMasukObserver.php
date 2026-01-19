<?php

namespace App\Observers;

use App\Models\LogBarangMasuk;
use App\Models\KartuGudang;

class LogBarangMasukObserver
{
    /**
     * Handle the LogBarangMasuk "created" event.
     */
    public function created(LogBarangMasuk $logBarangMasuk): void
    {
        if (!$logBarangMasuk->unit_kerja_id) {
            return;
        }

        KartuGudang::create([
            'kode_barang' => $logBarangMasuk->kode_barang,
            'nama_barang' => $logBarangMasuk->barang?->nama ?? $logBarangMasuk->kode_barang,
            'unit_kerja_id' => $logBarangMasuk->unit_kerja_id,
            'tanggal_keluar' => $logBarangMasuk->created_at,
            'jumlah_keluar' => $logBarangMasuk->jumlah,
            'sisa_stok' => $logBarangMasuk->barang?->sisa ?? 0,
            'jenis' => 'barang_masuk',
            'keterangan' => $logBarangMasuk->keterangan,
            'periode_tahun' => $logBarangMasuk->periode_tahun,
        ]);
    }

    /**
     * Handle the LogBarangMasuk "updated" event.
     */
    public function updated(LogBarangMasuk $logBarangMasuk): void
    {
        if (!$logBarangMasuk->unit_kerja_id) {
            KartuGudang::where('kode_barang', $logBarangMasuk->kode_barang)
                ->where('jenis', 'barang_masuk')
                ->where('created_at', $logBarangMasuk->created_at)
                ->delete();
            return;
        }

        KartuGudang::where('kode_barang', $logBarangMasuk->kode_barang)
            ->where('jenis', 'barang_masuk')
            ->where('created_at', $logBarangMasuk->created_at)
            ->update([
                'nama_barang' => $logBarangMasuk->barang?->nama ?? $logBarangMasuk->kode_barang,
                'unit_kerja_id' => $logBarangMasuk->unit_kerja_id,
                'tanggal_keluar' => $logBarangMasuk->created_at,
                'jumlah_keluar' => $logBarangMasuk->jumlah,
                'sisa_stok' => $logBarangMasuk->barang?->sisa ?? 0,
                'keterangan' => $logBarangMasuk->keterangan,
                'periode_tahun' => $logBarangMasuk->periode_tahun,
            ]);
    }

    /**
     * Handle the LogBarangMasuk "deleted" event.
     */
    public function deleted(LogBarangMasuk $logBarangMasuk): void
    {
        KartuGudang::where('kode_barang', $logBarangMasuk->kode_barang)
            ->where('jenis', 'barang_masuk')
            ->where('created_at', $logBarangMasuk->created_at)
            ->delete();
    }
}
