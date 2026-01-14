<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterByPeriodeTahun;

class Barang extends Model
{
    use FilterByPeriodeTahun;
    
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
        'kategori_id',
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
        'avatar',
        'periode_tahun',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }
}
