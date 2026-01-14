<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterByPeriodeTahun;

class LogPermintaan extends Model
{
    use HasFactory, FilterByPeriodeTahun;

    protected $table = 'log_permintaan';

    protected $fillable = [
        'permintaan_id',
        'status_lama',
        'status_baru',
        'user_id',
        'keterangan',
        'periode_tahun',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function permintaan()
    {
        return $this->belongsTo(Permintaan::class);
    }
}
