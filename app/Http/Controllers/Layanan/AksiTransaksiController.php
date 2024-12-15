<?php

namespace App\Http\Controllers\Layanan;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\Tagihan;
use Illuminate\Http\Request;
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
        
        // dd($penggunaanTagihan);

        return view('transaksis.detail', 
            [
                'detailPelanggan' => $pelangganInfo,
                'detailTagihan' => $detailtagihan,
                'paymentMethod' => $this->paymentMethod,
                'title' => $this->title,
                'breadcrumb' => $this->breadcrumb,
                'route' => $this->route,
                'primaryKey' => $this->primaryKey
        ]);
    }
}
