<?php

namespace App\Http\Controllers\Layanan;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Models\Tahun;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    protected $model = Pembayaran::class;
    protected $grid;
    protected $title;
    protected $breadcrumb;
    protected $route;
    protected $primaryKey = 'pembayaranId';

    public function __construct()
    {
        $this->title = 'Data Transaksi';
        $this->breadcrumb = 'Layanan';
        $this->route = 'transaksi';

        $this->grid = array(
            array(
                'label' => 'Kode Tagihan',
                'field' => 'tagihanKode',
            ),
            array(
                'label' => 'Nama Pelanggan',
                'field' => 'tagihanPelangganNama',
            ),
            array(
                'label' => 'Tagihan Terbit',
                'field' => 'tagihanTerbit',
                ),
            array(
                'label' => 'Meter Awal (m3)',
                'field' => 'tagihanMAwal',
                
            ),
            array(
                'label' => 'Meter Akhir (m3)',
                'field' => 'tagihanMAkhir',
                
            ),
            array(
                'label' => 'Status Tagihan',
                'field' => 'tagihanStatus',
                
            ),
        );
    }
    
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Tagihan::all();
            return datatables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        return '<div class="btn-group" role="group" aria-label="Basic example">
                                    <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$row->tagihanId.'" data-original-title="Bayar" class="bayar btn btn-success btn-xs"><i class="fa-solid fa-circle-dollar-to-slot"></i> Bayar</a>
                                </div>';
                    })
                    ->addColumn('tagihanJumlah', function($row){
                        $jumlah = Pembayaran::where('pembayaranTagihanId', $row->tagihanId)->first()->pembayaranJumlah;
                        return 'Rp ' . number_format($jumlah, 0, ',', '.');
                    })
                    ->addColumn('tagihanTerbit', function($row){
                        return $row->tagihanBulan . ' - ' . $row->tagihanTahun;
                    })
                    ->addColumn('tagihanPelangganNama', function($row){
                        return $row->pelanggan->pelangganNama;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }

        return view('transaksis.index', 
            [
                'grid' => $this->grid, 
                'title' => $this->title,
                'breadcrumb' => $this->breadcrumb,
                'route' => $this->route,
                'primaryKey' => $this->primaryKey
        ]);
    }
}
