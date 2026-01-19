<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterByPeriodeTahun;

class KartuGudang extends Model
{
    use FilterByPeriodeTahun;

    protected $table = 'kartu_gudang';
    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'unit_kerja_id',
        'tanggal_keluar',
        'jumlah_keluar',
        'sisa_stok',
        'jenis',
        'keterangan',
        'periode_tahun',
    ];

    protected $casts = [
        'tanggal_keluar' => 'datetime',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'kode_barang', 'kode');
    }

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class);
    }

    /**
     * Scope untuk mengambil data barang yang sudah di-group
     */
    public function scopeGroupByNamaBarang($query)
    {
        return $query->select('kode_barang', 'nama_barang')
            ->distinct()
            ->orderBy('nama_barang');
    }

    /**
     * Scope untuk mengambil riwayat barang tertentu
     */
    public function scopeByKodeBarang($query, $kodeBarang)
    {
        return $query->where('kode_barang', $kodeBarang)
            ->orderBy('tanggal_keluar', 'desc');
    }
}

