<?php

namespace App\Http\Controllers\Data;

use App\Http\Controllers\Controller;
use App\Models\Golongan;
use App\Models\Pelanggan;
use App\Models\Tagihan;
use App\Models\Tahun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class TagihanController extends Controller
{
    protected $model = Tahun::class;
    protected $grid;
    protected $title;
    protected $breadcrumb;
    protected $route;
    protected $primaryKey = 'pelangganKode';

    public function __construct()
    {
        $this->title = 'Data Tagihan';
        $this->breadcrumb = 'Layanan';
        $this->route = 'tagihan';

        $this->grid = array(
            array(
                'label' => 'Kode Pelanggan',
                'field' => 'pelangganKode',
            ),
            array(
                'label' => 'Pelanggan ID',
                'field' => 'pelangganNama',
            ),
            array(
                'label' => 'Golongan Tarif',
                'field' => 'pelangganGolonganId',
            ),
            [
                'label' => 'Tagihan Terakhir',
                'field' => 'tagihanTerakhir',
            ],
        );
    }
    
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Pelanggan::select('pelangganId', 'pelangganKode', 'pelangganNama', 'pelangganGolonganId')
                ->orderBy('created_at', 'desc')
                ->get();
            return datatables()::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $encodedKode = Crypt::encryptString($row->pelangganKode);
                    return '<div class="btn-group" role="group" aria-label="Basic example">
                                <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$encodedKode.'" data-original-title="Edit" class="edit btn btn-primary btn-xs"><i class="fa-regular fa-eye"></i></a>
                            </div>';
                })
                ->editColumn('pelangganGolonganId', function($row){
                    return Golongan::find($row->pelangganGolonganId)->golonganNama;
                })
                ->addColumn('tagihanTerakhir', function($row){
                    $tagihan = Tagihan::where('tagihanPelangganId', $row->pelangganId)->orderBy('created_at', 'desc')->first();
                    return $tagihan ? $tagihan->tagihanBulan . ' - ' . $tagihan->tagihanTahun : '-';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        

        return view('layanans.index', 
            [
                'form' => $this->grid, 
                'title' => $this->title,
                'breadcrumb' => $this->breadcrumb,
                'route' => $this->route,
                'primaryKey' => $this->primaryKey
        ]);
    }
}
