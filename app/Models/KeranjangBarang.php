<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeranjangBarang extends Model
{
    protected $table = 'keranjang';
    protected $fillable = ['user_id', 'kode', 'jumlah', 'periode_tahun'];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'kode', 'kode');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
