<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayarans';
    protected $primaryKey = 'pembayaranId';
    protected $fillable = [
        'pembayaranTagihanId',
        'pembayaranMetode',
        'pembayaranJumlah',
        'pembayaranUang',
        'pembayaranKembali',
        'pembayaranStatus',
        'pembayaranAbonemen',
        'pembayaranAdminFee',
        'pembayaranKasirId'
    ];

    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class, 'pembayaranTagihanId');
    }

    public function midtransPayment()
    {
        return $this->hasOne(MidtransPayment::class, 'midtransPaymentPembayaranId', 'pembayaranId');
    }

    public function duitkuPayment()
    {
        return $this->hasOne(DuitkuPG::class, 'payment_pembayaranId', 'pembayaranId');
    }

}


