<?php

namespace App\Http\Controllers\Data;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Golongan;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Models\Tahun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AksiTagihanController extends BaseController
{
    protected $model = Tagihan::class;
    protected $form;
    protected $title;
    protected $breadcrumb;
    protected $route;
    protected $primaryKey = 'tagihanId';

    public function __construct()
    {
        $this->title = 'Data Tagihan';
        $this->breadcrumb = 'Layanan';
        $this->route = 'aksi-tagihan';

        $this->form = array(
            array(
                'label' => 'Kode Tagihan',
                'field' => 'tagihanKode',
                'type' => 'text',
                'placeholder' => '',
                'width' => 6,
                'disabled' => true
            ),
            array(
                'label' => 'Bulan Tagihan',
                'field' => 'tagihanBulan',
                'type' => 'select',
                'placeholder' => 'Pilih Bulan',
                'width' => 6,
                'required' => true,
                'options' => [
                    'Januari' => 'Januari',
                    'Februari' => 'Februari',
                    'Maret' => 'Maret',
                    'April' => 'April',
                    'Mei' => 'Mei',
                    'Juni' => 'Juni',
                    'Juli' => 'Juli',
                    'Agustus' => 'Agustus',
                    'September' => 'September',
                    'Oktober' => 'Oktober',
                    'November' => 'November',
                    'Desember' => 'Desember'
                ]
            ),
            array(
                'label' => 'Tahun Tagihan',
                'field' => 'tagihanTahun',
                'type' => 'select',
                'placeholder' => 'Pilih Tahun',
                'width' => 6,
                'required' => true,
                'options' => Tahun::all()->pluck('tahun', 'tahun')->toArray(),
            ),
            array(
                'label' => 'Meter Awal',
                'field' => 'tagihanMAwal',
                'type' => 'number',
                'placeholder' => 'Masukkan Meter Awal',
                'width' => 6,
                'required' => true,
            ),
            array(
                'label' => 'Meter Akhir',
                'field' => 'tagihanMAkhir',
                'type' => 'number',
                'placeholder' => 'Masukkan Meter Akhir',
                'width' => 6,
                'required' => true,
            ),
            array(
                'label' => 'Status Tagihan',
                'field' => 'tagihanStatus',
                'type' => 'select',
                'placeholder' => 'Pilih Status',
                'width' => 6,
                'required' => true,
                'options' => [
                    'Lunas' => 'Lunas',
                    'Belum Lunas' => 'Belum Lunas',
                    'Pending' => 'Pending'
                ]
            ),
            array(
                'label' => 'Catatan Tagihan',
                'field' => 'tagihanCatatan',
                'type' => 'textarea',
                'placeholder' => 'Masukkan Catatan',
                'width' => 6,
            ),
        );
    }

    public function index(Request $request)
    {
        $tagihan = Pelanggan::where('pelangganKode', $request->pelangganKode)->first()->pelangganId;
        if ($request->ajax()) {
            $data = Tagihan::where('tagihanPelangganId', $tagihan)->get();
            return datatables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        return '<div class="btn-group" role="group" aria-label="Basic example">
                                    <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$row->tagihanId.'" data-original-title="Edit" class="edit btn btn-primary btn-xs"><i class="fa-regular fa-pen-to-square"></i></a>
                                    &nbsp;
                                    <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$row->tagihanId.'" data-original-title="Delete" class="delete btn btn-danger btn-xs"><i class="fa-solid fa-trash"></i></a>
                                </div>';
                    })
                    ->addColumn('tagihanJumlah', function($row){
                        $jumlah = Pembayaran::where('pembayaranTagihanId', $row->tagihanId)->first()->pembayaranJumlah;
                        return 'Rp ' . number_format($jumlah, 0, ',', '.');
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
    }
    
    public function show($pelangganKode)
    {
        $detailPelanggan = Pelanggan::where('pelangganKode', $pelangganKode)->first();
        $penggunaanTagihan = Tagihan::
            selectRaw('MAX(tagihanMAkhir) as tagihanMAkhir')
            ->selectRaw('SUM((tagihanMAkhir - tagihanMAwal) * tagihanInfoTarif) as totalTagihan')
            ->where('tagihanPelangganId', $detailPelanggan->pelangganId)
            ->first();
        
        // dd($penggunaanTagihan);

        return view('layanans.detail', 
            [
                'detailPelanggan' => $detailPelanggan,
                
                'penggunaanTagihan' => $penggunaanTagihan,
                'form' => $this->form, 
                'title' => $this->title,
                'breadcrumb' => $this->breadcrumb,
                'route' => $this->route,
                'primaryKey' => $this->primaryKey
        ]);
    }

    // Generate Unique Code Tagihan
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
        $rules = [];
        foreach ($this->form as $value) {
            if (isset($value['required']) && $value['required']) {
                $rules[$value['field']] = 'required';
            }
        }
        $request->validate($rules);

        $tagihan = Tagihan::create([
            'tagihanKode' => $this->generateUniqueCode(),
            'tagihanPelangganId' => $request->pelangganId,
            'tagihanBulan' => $request->tagihanBulan,
            'tagihanTahun' => $request->tagihanTahun,
            'tagihanInfoTarif' => Pelanggan::where('pelangganId', $request->pelangganId)->first()->golongan->golonganTarif,
            'tagihanInfoDenda' => Pelanggan::where('pelangganId', $request->pelangganId)->first()->golongan->golonganDenda,
            'tagihanMAwal' => $request->tagihanMAwal,
            'tagihanMAkhir' => $request->tagihanMAkhir,
            'tagihanUserId' => Auth::user()->id,
            'tagihanTanggal' => date('Y-m-d'),
            'tagihanStatus' => $request->tagihanStatus,
            'tagihanCatatan' => $request->tagihanCatatan,
        ]);

        Pembayaran::create([
            'pembayaranTagihanId' => $tagihan->tagihanId,
            'pembayaranJumlah' => ($request->tagihanMAkhir - $request->tagihanMAwal) * $tagihan->tagihanInfoTarif,
            'pembayaranStatus' => 'Belum Lunas'
        ]);

        return response()->json(['success' => 'Data Berhasil Disimpan']);
    }

    public function update(Request $request, $id)
    {
        $model = app($this->model);
        $data = $model->find($id);

        if (!$data) {
            return response()->json(['error' => 'Data not found'], 404);
        }

        $data->update($request->except(['pembayaranJumlah', 'pembayaranStatus']));

        $pembayaran = Pembayaran::where('pembayaranTagihanId', $data->tagihanId)->first();
        $pembayaran->update([
            'pembayaranJumlah' => ($data->tagihanMAkhir - $data->tagihanMAwal) * $data->tagihanInfoTarif,
            'pembayaranStatus' => 'Belum Lunas'
        ]);

        return response()->json(['message' => 'Data updated successfully', 'data' => $data]);
    }
}
