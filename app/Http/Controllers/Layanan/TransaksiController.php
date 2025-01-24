<?php

namespace App\Http\Controllers\Layanan;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Models\Tahun;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

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
            if (Auth::user()->userRoleId != 2) {
                $data = Tagihan::all();
            } else {
                $user = Pelanggan::where('pelangganUserId', Auth::user()->id)->first();
                $data = Tagihan::where('tagihanPelangganId', $user->pelangganId)->get();
            }
            return datatables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $tagihanId = Crypt::encryptString($row->tagihanId);
                        if ($row->tagihanStatus == 'Lunas') {
                            return '<div class="btn-group" role="group" aria-label="Basic example">
                                        <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$tagihanId.'" data-original-title="Lunas" class="bayar btn btn-primary btn-xs"><i class="fa-solid fa-circle-check"></i> Lihat</a>
                                    </div>';
                        } else {
                            return '<div class="btn-group" role="group" aria-label="Basic example">
                                        <a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$tagihanId.'" data-original-title="Bayar" class="bayar btn btn-success btn-xs"><i class="fa-solid fa-circle-dollar-to-slot"></i> Bayar</a>
                                    </div>';
                        }
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

    public function unduhStruk($id)
    {
        $tagihanId = Crypt::decryptString($id);
        $tagihan = Tagihan::findOrFail($tagihanId);
        // dd($tagihan);
        $pembayaran = Pembayaran::where('pembayaranTagihanId', $tagihanId)->firstOrFail();

        $data = [
            'tagihanKode' => $tagihan->tagihanKode,
            'pelangganKode' => $tagihan->pelanggan->pelangganKode,
            'pelangganNama' => $tagihan->pelanggan->pelangganNama,
            'tagihanMeteranAwal' => $tagihan->tagihanMAwal,
            'tagihanMeteranAkhir' => $tagihan->tagihanMAkhir,
            'nama_bulan' => $tagihan->tagihanBulan,
            'tagihanTahun' => $tagihan->tagihanTahun,
            'formattedTagihanTotal' => number_format($pembayaran->pembayaranJumlah, 0, ',', '.'),
            'formattedTotalDenda' => number_format($pembayaran->pembayaranDenda, 0, ',', '.'),
            'formattedTotal' => number_format($pembayaran->pembayaranJumlah + $pembayaran->pembayaranDenda, 0, ',', '.'),
            'date' => now()->format('d-m-Y'),
            'name' => "Kasir",
        ];
        // dd($data);

        $pdf = Pdf::loadView('transaksis.struk.index', compact('data'));
        return $pdf->download('struk-pembayaran-' . $data['pelangganKode'] . '-' . $data['tagihanKode'] . '.pdf');

    }
}
