<?php

namespace App\Http\Controllers\Layanan;

use App\Http\Controllers\Controller;
use App\Models\MidtransPayment;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransController extends Controller
{
    public function createSnapToken(Request $request)
    {
        $tagihan = Tagihan::find($request->tagihanId);

        if($tagihan->tagihanStatus == "Lunas" || $tagihan->tagihanStatus == "Pending")
        {
            return response()->json(['error' => "SUdah ada"], 500);
        }else{
            // Tangkap metode pembayaran yang dipilih pelanggan
            $selectedMethod = $request->input('paymentMethod'); // e.g., 'bank_transfer'

            // Konfigurasi Midtrans
            Config::$serverKey = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production');
            Config::$isSanitized = true;
            Config::$is3ds = true;

            // Tentukan metode pembayaran
            $paymentChannels = [
                'QRIS' => ['other_qris'],
                'BCA' => ['bca_va'],
                'ALFAMART' => ['alfamart'],
                'INDOMARET' => ['indomaret'],
            ];

            // Validasi metode pembayaran
            $enabledPayments = $paymentChannels[$selectedMethod] ?? [];
            // dd($enabledPayments);

            // Data transaksi
            $transactionData = [
                'transaction_details' => [
                    'order_id' => 'ORDER-' . $tagihan->tagihanId . '-' . time(),
                    'gross_amount' => $request->totalTagihan,
                ],
                'customer_details' => [
                    'first_name' => $tagihan->pelanggan->pelangganNama ?? '',
                    'email' => $tagihan->pelanggan->pelangganEmail ?? '',
                    'phone' => $tagihan->pelanggan->pelangganPhone ?? '',
                ],
                'enabled_payments' => $enabledPayments,
                'item_details' => [
                    [
                        'id' => $tagihan->tagihanId,
                        'price' => $request->totalTagihan,
                        'quantity' => 1,
                        'name' => 'Tagihan #' . $tagihan->tagihanKode,
                    ],
                ],
            ];

            try {
                $snapToken = Snap::getSnapToken($transactionData);

                $pembayaran = Pembayaran::where('pembayaranTagihanId', $tagihan->tagihanId)->first();
                if ($pembayaran) {
                    $pembayaran->pembayaranMetode = $selectedMethod;
                    $pembayaran->pembayaranDenda = $request->input('pembayaranDenda') ?? '0';
                    $pembayaran->pembayaranAdminFee = $request->input('pembayaranAdminFee') ?? '0';
                    $pembayaran->save();
                }

                MidtransPayment::updateOrCreate(
                    ['midtransPaymentPembayaranId' => $pembayaran->pembayaranId],
                    [
                        'midtransPaymentOrderId' => $transactionData['transaction_details']['order_id'],
                        'midtransPaymentSnapToken' => $snapToken,
                        'midtransPaymentStatus' => 'Pending'
                    ]
                );

                return response()->json([
                    'snap_token' => $snapToken,
                    'order_id' => $transactionData['transaction_details']['order_id']
                ]);


            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }
        
    }

    public function updateDatabase(Request $request)
    {
        $tagihan = Tagihan::find($request->tagihanId);

        MidtransPayment::updateOrCreate(
            ['midtransPaymentPembayaranId' => $tagihan->pembayaranInfo->pembayaranId],
            [
                'midtransPaymentOrderId' => $request->orderId,
                'midtransPaymentTransactionId' => $request->transactionId,
                'midtransPaymentSnapToken' => $request->snapToken,
                'midtransPaymentStatus' => $request->status,
            ]
        );

        if($request->status == "pending"){
            $tagihan['tagihanStatus'] = 'Pending';
            $tagihan->save();

            $pembayaran = Pembayaran::where('pembayaranTagihanId', $tagihan->tagihanId)->first();
            $pembayaran['pembayaranStatus'] = 'Pending';
            $pembayaran->save();

        } else {
            $tagihan['tagihanStatus'] = 'Lunas';
            $tagihan->save();

            $pembayaran = Pembayaran::where('pembayaranTagihanId', $tagihan->tagihanId)->first();
            $pembayaran['pembayaranStatus'] = 'Lunas';
            $pembayaran->save();
        }



        

    }

    public function handleNotification(Request $request)
    {
        // Ambil notifikasi dari Midtrans
        $notification = $request->all();

        // Cek status pembayaran
        $transactionStatus = $notification['transaction_status'];
        $orderId = $notification['order_id'];

        // Cari data di database berdasarkan order_id
        $midtransPayment = MidtransPayment::where('midtransPaymentOrderId', $orderId)->first();
        $tagihanId = Pembayaran::where('pembayaranId', $midtransPayment->midtransPaymentPembayaranId)->value('pembayaranTagihanId');

        $tagihan = Tagihan::findOrFail($tagihanId);


        if ($midtransPayment) {
            // Update status berdasarkan notifikasi
            switch ($transactionStatus) {
                case 'settlement':
                    $tagihan->update(['tagihanStatus' => 'Lunas']);
                    $midtransPayment->update(['midtransPaymentStatus' => 'success', 'midtransPaymentTransactionId' => $notification['transaction_id']]);
                    $midtransPayment->pembayaran->update(['pembayaranStatus' => 'Lunas']);
                    break;
                case 'pending':
                    $tagihan->update(['tagihanStatus' => 'Pending']);
                    $midtransPayment->update(['midtransPaymentStatus' => 'Pending', 'midtransPaymentTransactionId' => $notification['transaction_id']]);
                    break;
                case 'deny':
                case 'expire':
                case 'cancel':
                    $tagihan->update(['tagihanStatus' => 'Belum Lunas']);
                    $midtransPayment->update(['midtransPaymentStatus' => 'failed', 'midtransPaymentTransactionId' => 'Failed']);
                    $midtransPayment->pembayaran->update(['pembayaranStatus' => 'Gagal']);
                    break;
            }

            return response()->json(['message' => 'Notification handled'], 200);
        }

        return response()->json(['message' => 'Order not found'], 404);
    }
}