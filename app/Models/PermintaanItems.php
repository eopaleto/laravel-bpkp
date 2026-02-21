<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterByPeriodeTahun;

class PermintaanItems extends Model
{
    use HasFactory, FilterByPeriodeTahun;

    protected $table = 'permintaan_checkout_items';
    protected $fillable = [
        'permintaan_checkout_id',
        'nama_barang',
        'jumlah',
        'satuan',
        'harga_satuan',
        'subtotal',
        'periode_tahun',
    ];

    public function permintaan()
    {
        return $this->belongsTo(Permintaan::class, 'permintaan_checkout_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function unit()
    {
        return $this->belongsTo(UnitKerja::class);
    }
}
