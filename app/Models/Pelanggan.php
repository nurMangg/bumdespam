<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    protected $table = 'mspelanggan';

    protected $primaryKey = 'pelangganId';
    protected $fillable = [
        'pelangganKode',
        'pelangganNama',
        'pelangganEmail',
        'pelangganPhone',
        'pelangganAlamat',
        'pelangganDesa',
        'pelangganRt',
        'pelangganRw',
        'pelangganGolonganId',
        'pelangganStatus',
        'pelangganUserId',
    ];

    public function golongan()
    {
        return $this->belongsTo(Golongan::class, 'pelangganGolonganId', 'golonganId');
    }

    public function tagihan()
    {
        return $this->hasMany(Tagihan::class, 'tagihanPelangganId', 'pelangganId');
    }
}

