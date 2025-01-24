<?php

namespace App\Http\Controllers\Layanan;

use App\Http\Controllers\Controller;
use App\Models\HistoryWeb;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class AksiTransaksiController extends Controller
{
    protected $model = Tagihan::class;
    protected $title;
    protected $breadcrumb;
    protected $route;
    protected $primaryKey = 'tagihanId';
    protected $paymentMethod;

    public function __construct()
    {
        $this->title = 'Data Transaksi';
        $this->breadcrumb = 'Layanan';
        $this->route = 'aksi-tagihan';
        $this->paymentMethod = array(
            array(
                'label' => 'QRIS (0.7%)',
                'value' => 'QRIS',
                'price' => '0.7%'
            ),
            array(
                'label' => 'Bank BCA',
                'value' => 'BCA',
                'price' => '2500'
            ),
            array(
                'label' => 'Alfamart',
                'value' => 'ALFAMART',
                'price' => '3500'
            ),
            array(
                'label' => 'Indomaret',
                'value' => 'INDOMARET',
                'price' => '3500'
            ),
        );
    }


    public function show($tagihanId)
    {
        $decodeTagihanKode = Crypt::decryptString($tagihanId);
        
        $detailtagihan = Tagihan::where('tagihanId', $decodeTagihanKode)->first();
        $pelangganInfo = Pelanggan::where('pelangganId', $detailtagihan->tagihanPelangganId)->first();

        $detailTagihanCrypt = Crypt::encryptString($detailtagihan->tagihanId);
        
        // dd($penggunaanTagihan);

        return view('transaksis.detail', 
            [
                'detailPelanggan' => $pelangganInfo,
                'detailTagihan' => $detailtagihan,
                'tagihanIdCrypt' => $detailTagihanCrypt,
                'paymentMethod' => $this->paymentMethod,
                'title' => $this->title,
                'breadcrumb' => $this->breadcrumb,
                'route' => $this->route,
                'primaryKey' => $this->primaryKey
        ]);
    }

    public function pembayaranTunai(Request $request)
    {
        $tagihanId = $request->tagihanId;
        $decodeTagihanKode = Crypt::decryptString($tagihanId);
        // dd($decodeTagihanKode);

        $detailtagihan = Tagihan::where('tagihanId', $decodeTagihanKode)->first();

        if(!$detailtagihan){
            return response()->json(['error' => "Tagihan tidak ditemukan"], 500);
        }

        $pembayaran = Pembayaran::where('pembayaranTagihanId', $detailtagihan->tagihanId)->first();
        if ($pembayaran) {
            $detailtagihan->tagihanStatus = "Lunas";
            $detailtagihan->save();

            $pembayaran->pembayaranMetode = "Tunai";
            $pembayaran->pembayaranDenda = $request->input('pembayaranDenda') ?? '0';
            $pembayaran->pembayaranAdminFee = $request->input('pembayaranAdminFee') ?? '0';
            $pembayaran->pembayaranStatus = "Lunas";

            $pembayaran->save();

            HistoryWeb::create([
                'riwayatUserId' => Auth::user()->id,
                'riwayatTable' => 'Transaksi',
                'riwayatAksi' => 'Transaksi Tunai',
                'riwayatData' => json_encode($pembayaran),
            ]);
        }
    
    
    }
}
