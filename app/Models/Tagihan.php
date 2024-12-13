<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    protected $table = 'tagihans';
    protected $primaryKey = 'tagihanId';

    protected $fillable = [
        'tagihanKode',
        'tagihanPelangganId',
        'tagihanBulan',
        'tagihanTahun',
        'tagihanInfoTarif',
        'tagihanInfoDenda',
        'tagihanMAwal',
        'tagihanMAkhir',
        'tagihanUserId',
        'tagihanTanggal',
        'tagihanStatus',
        'tagihanCatatan'
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'tagihanPelangganId');
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'pembayaranTagihanId');
    }


}
