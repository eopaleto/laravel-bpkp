<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanItems extends Model
{
    use HasFactory;

    protected $table = 'permintaan_checkout_items';
    protected $fillable = [
        'permintaan_checkout_id',
        'nama_barang',
        'jumlah',
        'harga_satuan',
        'subtotal',
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
