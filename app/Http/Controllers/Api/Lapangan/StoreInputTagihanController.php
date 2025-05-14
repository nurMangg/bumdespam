<?php

namespace App\Http\Controllers\Api\Lapangan;

use App\Http\Controllers\Controller;
use App\Models\HistoryWeb;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StoreInputTagihanController extends Controller
{
    
    public function generateUniqueCode(): string
    {
        $date = date('ym');
        $tagihanCount = Tagihan::whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->count();
        $tagihanPart = str_pad($tagihanCount + 1, 4, '0', STR_PAD_LEFT);
        return "TPAM-{$date}{$tagihanPart}";
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pelangganKode' => 'required',
            'tagihanMAkhir' => 'required',
            'tagihanBulan' => 'required',
            'tagihanTahun' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }


        $pelanggan = Pelanggan::where('pelangganKode', $request->pelangganKode)->first();
        if(!$pelanggan) {
            return response()->json(['errors' => "Tidak Ada Data Pelanggan", 'status'=> 'Error'], 422);
        }

        $dataTagihan = Tagihan::where('tagihanPelangganId', $pelanggan->pelangganId)
            ->orderBy('tagihanTahun', 'desc')
            ->orderBy('tagihanBulan', 'desc')
            ->first();

        $newtagihan = Tagihan::create([
            'tagihanKode' => $this->generateUniqueCode(),
            'tagihanPelangganId' => $pelanggan->pelangganId,
            'tagihanBulan' => $request->tagihanBulan,
            'tagihanTahun' => $request->tagihanTahun,
            'tagihanInfoTarif' => $pelanggan->golongan->golonganTarif,
            'tagihanInfoAbonemen' => $pelanggan->golongan->golonganAbonemen,
            'tagihanMAwal' => ($dataTagihan->tagihanMAkhir ?? 0) + 1,
            'tagihanMAkhir' => $request->tagihanMAkhir,
            'tagihanUserId' => 1,
            'tagihanTanggal' => date('Y-m-d'),
            'tagihanStatus' => "Belum Lunas",
        ]);

        Pembayaran::create([
            'pembayaranTagihanId' => $newtagihan->tagihanId,
            'pembayaranJumlah' => ($newtagihan->tagihanMAkhir - $newtagihan->tagihanMAwal) * $newtagihan->tagihanInfoTarif,
            'pembayaranStatus' => 'Belum Lunas'
        ]);

        HistoryWeb::create([
            'riwayatUserId' => 1,
            'riwayatTable' => 'Tagihan',
            'riwayatAksi' => 'Input Tagihan',
            'riwayatData' => json_encode($newtagihan),
        ]);

        // if ($pelanggan && $pelanggan->pelangganPhone) {
        //     try {
        //         $namaBulan = Bulan::where('bulanId', $newtagihan->tagihanBulan)->value('bulanNama');
        //         $this->send_message($pelanggan->pelangganPhone, $pelanggan->pelangganNama, $namaBulan, $newtagihan->tagihanTahun);
        //         Log::info("Pesan berhasil dikirim.");
        //     } catch (\Exception $e) {
        //         Log::error("Gagal mengirim pesan: " . $e->getMessage());
        //     }
        // } else {
        //     Log::warning("Nomor pelanggan tidak ditemukan.");
        // }

        return response()->json(['success' => 'Tagihan Berhasil Disimpan']);
    }
}
