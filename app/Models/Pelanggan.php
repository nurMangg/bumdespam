<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
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
}
