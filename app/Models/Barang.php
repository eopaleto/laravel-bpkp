<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'barang';
    protected $primaryKey = 'kode';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'kode',
        'sku',
        'nama',
        'hargabeli',
        'hargajual',
        'keterangan',
        'kategori',
        'satuan',
        'terjual',
        'terbeli',
        'sisa',
        'stokmin',
        'barcode',
        'brand',
        'lokasi',
        'expired',
        'warna',
        'ukuran',
        'avatar',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }
}
