<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permintaan extends Model
{
    use HasFactory;

    protected $table = 'permintaan_checkout';
    protected $fillable = [
        'user_id',
        'total',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function TotalBarang()
    {
        return $this->items->sum('jumlah');
    }

    public function items()
    {
        return $this->hasMany(PermintaanItems::class, 'permintaan_checkout_id');
    }
}
