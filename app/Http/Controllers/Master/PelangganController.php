<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Golongan;
use App\Models\HistoryWeb;
use App\Models\Pelanggan;
use App\Models\User;
use Faker\Provider\Base;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class PelangganController extends BaseController
{
    protected $model = Pelanggan::class;
    protected $form;
    protected $title;
    protected $breadcrumb;
    protected $route;
    protected $primaryKey = 'pelangganId';

    public function __construct()
    {
        $this->title = 'Pelanggan';
        $this->breadcrumb = 'Master Data';
        $this->route = 'pelanggan';

        $this->form = array(
            array(
                'label' => 'Kode Pelanggan',
                'field' => 'pelangganKode',
                'type' => 'text',
                'placeholder' => '',
                'width' => 6,
                'disabled' => true,
            ),
            array(
                'label' => 'Nama Pelanggan',
                'field' => 'pelangganNama',
                'type' => 'text',
                'placeholder' => 'Masukkan Nama',
                'width' => 6,
                'required' => true

            ),
            array(
                'label' => 'Email',
                'field' => 'pelangganEmail',
                'type' => 'email',
                'placeholder' => 'Masukkan Email',
                'width' => 6,
                'required' => true
            ),
            array(
                'label' => 'No. Telepon',
                'field' => 'pelangganPhone',
                'type' => 'number',
                'placeholder' => 'Masukkan No. Telepon',
                'width' => 6,
                'required' => true
            ),
            array(
                'label' => 'Alamat',
                'field' => 'pelangganAlamat',
                'type' => 'textarea',
                'placeholder' => 'Masukkan Alamat',
                'width' => 6,
                'required' => true
            ),
            array(
                'label' => 'Desa',
                'field' => 'pelangganDesa',
                'type' => 'select',
                'placeholder' => 'Pilih Desa',
                'width' => 6,
                'required' => true,
                'options' => \App\Models\Desa::all()->pluck('desaNama', 'desaNama')->toArray()
            ),
            array(
                'label' => 'RT',
                'field' => 'pelangganRt',
                'type' => 'number',
                'placeholder' => 'Masukkan RT',
                'width' => 3,
                'required' => true
            ),
            array(
                'label' => 'RW',
                'field' => 'pelangganRw',
                'type' => 'number',
                'placeholder' => 'Masukkan RW',
                'width' => 3,
                'required' => true
            ),
            array(
                'label' => 'Golongan',
                'field' => 'pelangganGolonganId',
                'type' => 'select',
                'width' => 6,
                'placeholder' => 'Pilih Golongan',
                'required' => true,
                'options' => Golongan::pluck('golonganNama', 'golonganId')->toArray()

            ),
            array(
                'label' => 'Status',
                'field' => 'pelangganStatus',
                'type' => 'select',
                'width' => 6,
                'placeholder' => 'Pilih Status',
                'required' => true,
                'options' => [
                    'Aktif' => 'Aktif',
                    'Tidak Aktif' => 'Tidak Aktif'
                ]

            ),
        );
    }

    // Generate Unique Code Pelanggan
    public function generateUniqueCode(): string
    {
        $date = date('ym');
        $pelangganCount = Pelanggan::whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->count();

        $pelangganPart = str_pad($pelangganCount + 1, 3, '0', STR_PAD_LEFT);

        return "PAM{$date}{$pelangganPart}";
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Pelanggan::all();
            return datatables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $pelangganId = Crypt::encryptString($row->pelangganId);

                        return '<div class="btn-group" role="group" aria-label="Basic example">
                                    <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$pelangganId.'" data-original-title="View" class="view btn btn-warning btn-xs"><i class="fa-regular fa-eye"></i></a>
                                    &nbsp;
                                    <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$pelangganId.'" data-original-title="Edit" class="edit btn btn-primary btn-xs"><i class="fa-regular fa-pen-to-square"></i></a>
                                    &nbsp;
                                    <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$pelangganId.'" data-original-title="Delete" class="delete btn btn-danger btn-xs"><i class="fa-solid fa-trash"></i></a>
                                </div>';
                    })
                    ->editColumn('pelangganGolonganId', function ($row) {
                        return $row->golongan->golonganNama;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }

        return view('masters.index', 
            [
                'form' => $this->form, 
                'title' => $this->title,
                'breadcrumb' => $this->breadcrumb,
                'route' => $this->route,
                'primaryKey' => $this->primaryKey
        ]);
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

        $newPelanggan = Pelanggan::create([
            'pelangganKode' => $this->generateUniqueCode(),
            'pelangganNama' => $request->pelangganNama,
            'pelangganEmail' => $request->pelangganEmail,
            'pelangganPhone' => $request->pelangganPhone,
            'pelangganAlamat' => $request->pelangganAlamat,
            'pelangganDesa' => $request->pelangganDesa,
            'pelangganRt' => $request->pelangganRt,
            'pelangganRw' => $request->pelangganRw,
            'pelangganGolonganId' => $request->pelangganGolonganId,
            'pelangganStatus' => $request->pelangganStatus,
            'pelangganUserId' => Auth::user()->id
        ]);

        User::create([
            'name' => $request->pelangganNama,
            'email' => $request->pelangganEmail,
            'password' => Hash::make('password'),
            'userRoleId' => 2
        ]);

        HistoryWeb::create([
            'riwayatUserId' => Auth::user()->id,
            'riwayatTable' => 'Pelanggan',
            'riwayatAksi' => 'Tambah Data',
            'riwayatData' => json_encode($newPelanggan),
        ]);

        return response()->json(['success' => 'Data Berhasil Disimpan']);
    }

    public function viewKartu($id)
    {
        $decodeId = Crypt::decryptString($id);

        $model = app($this->model);
        $data = $model->find($decodeId);

        if (!$data) {
            return response()->json(['error' => 'Data not found'], 404);
        }

        // dd($data);
        return view('masters.cardnama.index', ['data'=> $data]);
    }
}
