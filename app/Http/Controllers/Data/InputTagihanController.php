<?php

namespace App\Http\Controllers\Data;

use App\Http\Controllers\Controller;
use App\Models\Bulan;
use App\Models\Golongan;
use App\Models\HistoryWeb;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class InputTagihanController extends Controller
{
    protected $model = Tagihan::class;
    protected $form;
    protected $form2;
    protected $form3;

    protected $title;
    protected $breadcrumb;
    protected $route;
    protected $primaryKey = 'tagihanId';

    public function __construct()
    {
        $this->title = 'Input Tagihan';
        $this->breadcrumb = 'Input Tagihan';
        $this->route = 'input-tagihan';

        $this->form = array(
            array(
                'label' => 'Kode Pelanggan',
                'field' => 'pelangganKode',
                'type' => 'text',
                'width' => 6,
                'disabled' => true,
            ),
            array(
                'label' => 'Nama Pelanggan',
                'field' => 'pelangganNama',
                'type' => 'text',
                'width' => 6,
                'disabled' => true

            ),
            array(
                'label' => 'Desa',
                'field' => 'pelangganDesa',
                'type' => 'text',
                'width' => 6,
                'disabled' => true,
            ),
            array(
                'label' => 'RT',
                'field' => 'pelangganRt',
                'type' => 'number',
                'width' => 3,
                'disabled' => true
            ),
            array(
                'label' => 'RW',
                'field' => 'pelangganRw',
                'type' => 'number',
                'width' => 3,
                'disabled' => true
            ),
            array(
                'label' => 'Golongan',
                'field' => 'pelangganGolonganId',
                'type' => 'text',
                'width' => 6,
                'disabled' => true,

            ),
            array(
                'label' => 'Status',
                'field' => 'pelangganStatus',
                'type' => 'text',
                'width' => 6,
                'disabled' => true,
            ),
        );

        $this->form2 = array(
            array(
                'label' => 'Bulan Tagihan',
                'field' => 'tagihanBulanBaru',
                'type' => 'text',
                'width' => 6,
                'disabled' => true,
            ),
            array(
                'label' => 'Tahun Tagihan',
                'field' => 'tagihanTahunBaru',
                'type' => 'text',
                'width' => 6,
                'disabled' => true

            ),
            array(
                'label' => 'Meter Awal',
                'field' => 'tagihanMeterAwal',
                'type' => 'number',
                'width' => 6,
                'disabled' => true
            ),
            array(
                'label' => 'Meter Akhir',
                'field' => 'tagihanMeterAkhir',
                'type' => 'number',
                'width' => 6,
                'disabled' => false
            ),
            array(
                'label' => 'Catatan Tagihan',
                'field' => 'tagihanKeterangan',
                'type' => 'textarea',
                'width' => 12,
                'disabled' => false
            ),
        );

        $this->form3 = array(
            array(
                'label' => 'Tagihan Terakhir',
                'field' => 'tagihanTerakhir',
                'type' => 'text',
                'width' => 12,
                'disabled' => true,
            ),
            array(
                'label' => 'Tagihan Meter Bulan Lalu',
                'field' => 'tagihanBulanLalu',
                'type' => 'text',
                'width' => 12,
                'disabled' => true

            ),
        );
    }

    public function index()
    {
        return view('input-tagihan.index', [
            'form' => $this->form,
            'form2' => $this->form2,
            'form3' => $this->form3,
            'title' => $this->title,
            'breadcrumb' => $this->breadcrumb,
            'route' => $this->route,
        ]);
    }

    public function show($id){
        // dd($id);
        $pelanggan = Pelanggan::where('pelangganKode', $id)->first();
        if(!$pelanggan) {
            return response()->json(['errors' => "Tidak Ada Data Pelanggan", 'status'=> 'Error'], 422);
        }
        // dd($pelanggan);

        $dataTagihan = Tagihan::where('tagihanPelangganId', $pelanggan->pelangganId)
            ->orderBy('created_at', 'desc')
            // ->orderBy('tagihanBulan', 'desc')
            ->first();

        // dd($dataTagihan);

        if(!$dataTagihan){
            return response()->json(['message' => 'Data tidak ditemukan', 'status'=> 'Error'], 403);
        }

        $idBulan = Bulan::where('bulan', $dataTagihan->tagihanBulan)->value('id');

        $tahunBaru = $dataTagihan->tagihanTahun;

        if($idBulan < 12){
            $idBulan = $idBulan + 1;
        }else{
            $idBulan = 1;
            $tahunBaru = (int) $dataTagihan->tagihanTahun + 1;

        }

        $dataTagihan['tagihanBulanBaru'] = Bulan::where('id', $idBulan)->value('bulan');

        $data = [
            'pelangganId' => Crypt::encryptString($pelanggan->pelangganKode),
            'pelangganKode' => $dataTagihan->pelanggan->pelangganKode,
            'pelangganNama' => $dataTagihan->pelanggan->pelangganNama,
            'pelangganDesa' => $dataTagihan->pelanggan->pelangganDesa,
            'pelangganRt' => $dataTagihan->pelanggan->pelangganRt,
            'pelangganRw' => $dataTagihan->pelanggan->pelangganRw,
            'pelangganGolonganId' => Golongan::where('golonganId', $dataTagihan->pelanggan->pelangganGolonganId)->value('golonganNama'),
            'pelangganStatus' => $dataTagihan->pelanggan->pelangganStatus,
            'tagihanBulanBaru' => $dataTagihan->tagihanBulanBaru,
            'tagihanTahunBaru' => $tahunBaru,
            'tagihanMeterAwal' => $dataTagihan->tagihanMAkhir + 1,
            'tagihanMeterAkhir' => '',
            'tagihanKeterangan' => $dataTagihan->tagihanKeterangan,
            'tagihanTerakhir' => $dataTagihan->tagihanBulan . ' - ' . $dataTagihan->tagihanTahun,
            'tagihanBulanLalu' => $dataTagihan->tagihanMAwal . ' - ' . $dataTagihan->tagihanMAkhir,
        ];
        
        // dd($data);

        return response()->json(['message' => 'Data Ditemukan', 'status'=> 'success', 'data' => $data], 200);
    }

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
            'id' => 'required',
            'meterAkhir' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $pelanggan = Pelanggan::where('pelangganKode', $request->id)->first();
        if(!$pelanggan) {
            return response()->json(['errors' => "Tidak Ada Data Pelanggan", 'status'=> 'Error'], 422);
        }
        // dd($pelanggan);

        $dataTagihan = Tagihan::where('tagihanPelangganId', $pelanggan->pelangganId)
            ->orderBy('tagihanTahun', 'desc')
            ->orderBy('tagihanBulan', 'desc')
            ->first();

        if(!$dataTagihan){
            return response()->json(['message' => 'Data tidak ditemukan', 'status'=> 'Error'], 403);
        }

        $idBulan = Bulan::where('bulan', $dataTagihan->tagihanBulan)->value('id');

        $tahunBaru = $dataTagihan->tagihanTahun;

        if($idBulan < 12){
            $idBulan = $idBulan + 1;
        }else{
            $idBulan = 1;
            $tahunBaru = (int) $dataTagihan->tagihanTahun + 1;

        }

        $dataTagihan['tagihanBulanBaru'] = Bulan::where('id', $idBulan)->value('bulan');

        
        $newtagihan = Tagihan::create([
            'tagihanKode' => $this->generateUniqueCode(),
            'tagihanPelangganId' => $pelanggan->pelangganId,
            'tagihanBulan' => $dataTagihan->tagihanBulanBaru,
            'tagihanTahun' => $tahunBaru,
            'tagihanInfoTarif' => $pelanggan->golongan->golonganTarif,
            'tagihanInfoDenda' => $pelanggan->golongan->golonganTarif,
            'tagihanMAwal' => $dataTagihan->tagihanMAkhir + 1,
            'tagihanMAkhir' => $request->meterAkhir,
            'tagihanUserId' => Auth::user()->id,
            'tagihanTanggal' => date('Y-m-d'),
            'tagihanStatus' => "Belum Lunas",
        'tagihanCatatan' => $request->catatanTagihan,
        ]);

        Pembayaran::create([
            'pembayaranTagihanId' => $newtagihan->tagihanId,
            'pembayaranJumlah' => ($newtagihan->tagihanMAkhir - $newtagihan->tagihanMAwal) * $newtagihan->tagihanInfoTarif,
            'pembayaranStatus' => 'Belum Lunas'
        ]);

        HistoryWeb::create([
            'riwayatUserId' => Auth::user()->id,
            'riwayatTable' => 'Tagihan',
            'riwayatAksi' => 'Input Tagihan',
            'riwayatData' => json_encode($newtagihan),
        ]);

        return response()->json(['success' => 'Tagihan Berhasil Disimpan']);

    }



    public function scanQRCode(Request $request) {

        $validator = Validator::make($request->all(), [
            'idPelanggan' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $pelanggan = Pelanggan::where('pelangganKode', $request->idPelanggan)->first();
        if(!$pelanggan) {
            return response()->json(['errors' => "Tidak Ada Data Pelanggan", 'status'=> 'Error'], 422);
        }

        $dataTagihan = Tagihan::where('tagihanPelangganId', $pelanggan->pelangganId)
            ->orderBy('tagihanTahun', 'desc')
            ->orderBy('tagihanBulan', 'desc')
            ->first();

        if(!$dataTagihan){
            return response()->json(['message' => 'Data tidak ditemukan', 'status'=> 'Error'], 403);
        }

        $idBulan = Bulan::where('bulan', $dataTagihan->tagihanBulan)->value('id');

        $tahunBaru = $dataTagihan->tagihanTahun;

        if($idBulan < 12){
            $idBulan = $idBulan + 1;
        }else{
            $idBulan = 1;
            $tahunBaru = (int) $dataTagihan->tagihanTahun + 1;

        }

        $dataTagihan['tagihanBulanBaru'] = Bulan::where('id', $idBulan)->value('bulan');

        $data = [
            'pelangganId' => Crypt::encryptString($pelanggan->pelangganId),
            'pelangganKode' => $dataTagihan->pelanggan->pelangganKode,
            'pelangganNama' => $dataTagihan->pelanggan->pelangganNama,
            'pelangganDesa' => $dataTagihan->pelanggan->pelangganDesa,
            'pelangganRt' => $dataTagihan->pelanggan->pelangganRt,
            'pelangganRw' => $dataTagihan->pelanggan->pelangganRw,
            'pelangganGolonganId' => Golongan::where('golonganId', $dataTagihan->pelanggan->pelangganGolonganId)->value('golonganNama'),
            'pelangganStatus' => $dataTagihan->pelanggan->pelangganStatus,
            'tagihanBulanBaru' => $dataTagihan->tagihanBulanBaru,
            'tagihanTahunBaru' => $tahunBaru,
            'tagihanMeterAwal' => $dataTagihan->tagihanMAkhir + 1,
            'tagihanMeterAkhir' => '',
            'tagihanKeterangan' => $dataTagihan->tagihanKeterangan,
            'tagihanTerakhir' => $dataTagihan->tagihanBulan . ' - ' . $dataTagihan->tagihanTahun,
            'tagihanBulanLalu' => $dataTagihan->tagihanMAwal . ' - ' . $dataTagihan->tagihanMAkhir,
        ];
        
        // dd($data);

        return response()->json(['message' => 'Data Ditemukan', 'status'=> 'success', 'data' => $data], 200);
    }
}
