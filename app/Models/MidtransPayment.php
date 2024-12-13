<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MidtransPayment extends Model
{
    protected $table = 'midtrans_payments';
    protected $primaryKey = 'midtransPaymentId';

    protected $fillable = [
        'midtransPaymentPembayaranId',
        'midtransPaymentOrderId',
        'midtransPaymentTransactionId',
        'midtransPaymentStatus',
        'midtransPaymentCatatan',
    ];

    public function pembayaran()
    {
        return $this->belongsTo(Pembayaran::class, 'midtransPaymentPembayaranId');
    }

}
