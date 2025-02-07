<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Silvanix\Wablas\Message;

abstract class Controller
{
    public function send_message($phones, $nama_pelanggan, $bulan, $tahun)
    {
        $send = new Message();
    
        // $phones = '085713050749';
    
        // // Data tagihan (bisa diambil dari database)
        // $nama_pelanggan = 'Nur Rohman';
        // $bulan = 'Januari';
        // $tahun = '2025';
        $link = 'https://pdam.withmangg.my.id';
    
        // Pesan utama dengan tambahan informasi tagihan
        $message = "*Tagihan PDAM Anda Sudah Tersedia!*\n\n"
                  ."Halo, *$nama_pelanggan*! 👋\n\n"
                  ."📅 Tagihan Anda bulan $bulan - $tahun sudah tersedia!. \n\n"
                  ."Segera lakukan pembayaran untuk memastikan layanan tetap berjalan lancar.\n"
                  ."Pembayaran dapat dilakukan melalui metode yang tersedia.\n\n"
                  ."🔗 *Cek tagihan dan bayar sekarang:* $link \n\n"
                  ."Terima kasih telah menggunakan layanan kami! \n\n"
                  ."—\n"
                  ."🔹 *PDAM BUMDES PAGAR SEJAHTERA* 🔹";
    
        // Mengirim pesan ke WhatsApp melalui Wablas
        $send_text = $send->single_text($phones, $message);
        // return response()->json($send_text);
        Log::info("Response dari Wablas: " . json_encode($send_text));
    }
}
