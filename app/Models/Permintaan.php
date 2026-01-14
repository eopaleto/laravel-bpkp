<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterByPeriodeTahun;

class Permintaan extends Model
{
    use HasFactory, FilterByPeriodeTahun;

    protected $table = 'permintaan_checkout';
    protected $fillable = [
        'user_id',
        'total',
        'status',
        'periode_tahun',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class);
    }

    public function TotalBarang()
    {
        return $this->items->sum('jumlah');
    }
    public function items()
    {
        return $this->hasMany(PermintaanItems::class, 'permintaan_checkout_id');
    }

    public function logPermintaan()
    {
        return $this->hasMany(LogPermintaan::class);
    }
}
