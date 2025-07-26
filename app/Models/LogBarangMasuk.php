<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogBarangMasuk extends Model
{
    protected $table = 'log_barang_masuk';
    protected $fillable = [
        'kode_barang',
        'unit_kerja_id',
        'jumlah',
        'keterangan',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'kode_barang', 'kode');
    }

    public function unit_kerja()
    {
        return $this->belongsTo(UnitKerja::class);
    }
}
