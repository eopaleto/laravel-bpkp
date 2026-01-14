<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterByPeriodeTahun;

class LogBarangKeluar extends Model
{
    use FilterByPeriodeTahun;
    protected $table = 'log_barang_keluar';
    protected $fillable = [
        'kode_barang',
        'unit_kerja_id',
        'jumlah',
        'user_id',
        'sisa_stok_saat_itu',
        'keterangan',
        'periode_tahun',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'kode_barang', 'kode');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function unit_kerja()
    {
        return $this->belongsTo(UnitKerja::class);
    }
}
