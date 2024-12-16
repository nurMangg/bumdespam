<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\Golongan;
use App\Models\Pelanggan;
use App\Models\Tagihan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LaporanTagihanController extends Controller
{
    protected $model = Tagihan::class;
    protected $grid;
    protected $form;

    protected $title;
    protected $breadcrumb;
    protected $route;
    protected $primaryKey = 'pelangganKode';

    public function __construct()
    {
        $this->title = 'Laporan Tagihan';
        $this->breadcrumb = 'Laporan';
        $this->route = 'laporan-tagihan';

        $this->form = array(
            array(
                'label' => 'Bulan',
                'field' => 'tagihanBulan',
                'type' => 'select',
                'options' => Tagihan::all()->pluck('tagihanBulan', 'tagihanBulan')->unique()->sort()->toArray(),
                'placeholder' => 'Semua Bulan',
                'width' => 6,
                'required' => true
            ),
            array(
                'label' => 'Desa',
                'field' => 'tagihanDesa',
                'type' => 'select',
                'options' => Pelanggan::all()->pluck('pelangganDesa', 'pelangganDesa')->unique()->sort()->toArray(), // Add option values here
                'placeholder' => 'Semua Tahun',
                'width' => 3,
                'required' => true
            ),
            array(
                'label' => 'Tahun',
                'field' => 'tagihanTahun',
                'type' => 'select',
                'options' => Tagihan::all()->pluck('tagihanTahun', 'tagihanTahun')->unique()->sort()->toArray(), // Add option values here
                'placeholder' => 'Semua Tahun',
                'width' => 3,
                'required' => true
            ),
            array(
                'label' => 'RT',
                'field' => 'pelangganRt',
                'type' => 'select',
                'options' => Pelanggan::all()->pluck('pelangganRt', 'pelangganRt')->unique()->sort()->toArray(), 
                'placeholder' => 'Semua RW',
                'width' => 3,
                'required' => true
            ),
            array(
                'label' => 'RW',
                'field' => 'pelangganRw',
                'type' => 'select',
                'options' => Pelanggan::all()->pluck('pelangganRw', 'pelangganRw')->unique()->sort()->toArray(), 
                'placeholder' => 'Semua RW',
                'width' => 3,
                'required' => true
            ),
            
            array(
                'label' => 'Status',
                'field' => 'tagihanStatus',
                'type' => 'select',
                'width' => 6,
                'placeholder' => 'Semua Status',
                'required' => true,
                'options' => [
                    'Belum Lunas' => 'Belum Lunas',
                    'Lunas' => 'Lunas',
                    'Pending' => 'Pending'
                ]

            ),
        );

        $this->grid = array(
            array(
                'label' => 'Kode Tagihan',
                'field' => 'tagihanKode',
            ),
            array(
                'label' => 'Nama',
                'field' => 'pelangganNama',
            ),
            array(
                'label' => 'Alamat',
                'field' => 'pelangganAlamat',
            ),
            array(
                'label' => 'RT/RW',
                'field' => 'pelangganRTRW',
            ),
            array(
                'label' => 'Meteran Awal (m3)',
                'field' => 'tagihanMAwal',
            ),
            array(
                'label' => 'Meteran Awal (m3)',
                'field' => 'tagihanMAkhir',
            ),
            array(
                'label' => 'Penggunaan Air (m3)',
                'field' => 'tagihanPenggunaan',
            ),
            array(
                'label' => 'Tagihan Terbit',
                'field' => 'tagihanTerbit',
            ),
            array(
                'label' => 'tagihan Status',
                'field' => 'tagihanStatus',
            ),
            
            
        );
    }
    
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Tagihan::with('pelanggan')->select('*')
                ->orderBy('created_at', 'desc');

                if ($request->has('filter')) {
                    // Apply filters specified in the request
                    $filters = $request->input('filter');
                    foreach ($filters as $field => $value) {
                        if (!empty($value)) {
                            if ($field === 'pelangganNama') {
                                // Filter berdasarkan nama pelanggan dari tabel relasi
                                $query->whereHas('pelanggan', function ($q) use ($value) {
                                    $q->where('pelangganNama', 'like', '%' . $value . '%');
                                });
                            } elseif ($field === 'tagihanBulan') {
                                // Filter berdasarkan bulan tagihan
                                $query->where('tagihanBulan', $value);
                            } elseif ($field === 'tagihanDesa') {
                                // Filter berdasarkan desa pelanggan
                                $query->whereHas('pelanggan', function ($q) use ($value) {
                                    $q->where('pelangganDesa', $value);
                                });
                            } elseif ($field === 'tagihanTahun') {
                                // Filter berdasarkan tahun tagihan
                                $query->where('tagihanTahun', $value);
                            } elseif ($field === 'pelangganRt') {
                                // Filter berdasarkan rt pelanggan
                                $query->whereHas('pelanggan', function ($q) use ($value) {
                                    $q->where('pelangganRt', $value);
                                });
                            } elseif ($field === 'pelangganRw') {
                                // Filter berdasarkan rw pelanggan
                                $query->whereHas('pelanggan', function ($q) use ($value) {
                                    $q->where('pelangganRw', $value);
                                });
                            } else {
                                // Filter untuk kolom lain di tabel tagihans
                                $query->where($field, 'like', '%' . $value . '%');
                            }
                        }
                    }
                }

            $data = $query->get();

            return datatables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('pelangganNama', function($row){
                        return $row->pelanggan->pelangganNama;
                    })
                    ->addColumn('pelangganAlamat', function($row){
                        return $row->pelanggan->pelangganDesa;
                    })
                    ->addColumn('pelangganRTRW', function($row){
                        return $row->pelanggan->pelangganRt . ' / ' . $row->pelanggan->pelangganRw;
                    })
                    ->addColumn('tagihanTerbit', function($row){
                        return $row->tagihanBulan . ' - ' . $row->tagihanTahun;
                    })
                    ->addColumn('tagihanPenggunaan', function($row){
                        return $row->tagihanMAkhir - $row->tagihanMAwal . ' m3';
                    })
                    ->make(true);
        }

        return view('laporans.index', 
            [
                'form' => $this->form,
                'grid' => $this->grid, 
                'title' => $this->title,
                'breadcrumb' => $this->breadcrumb,
                'route' => $this->route,
                'primaryKey' => $this->primaryKey
        ]);
    }

    public function exportPdf(Request $request)
    {
        $filters = $request->input('filter', []);
        $query = Tagihan::with('pelanggan')->select('*')
                ->orderBy('created_at', 'desc');

        foreach ($filters as $field => $value) {
            if (!empty($value)) {
                if ($field === 'pelangganNama') {
                    // Filter berdasarkan nama pelanggan dari tabel relasi
                    $query->whereHas('pelanggan', function ($q) use ($value) {
                        $q->where('pelangganNama', 'like', '%' . $value . '%');
                    });
                } elseif ($field === 'tagihanBulan') {
                    // Filter berdasarkan bulan tagihan
                    $query->where('tagihanBulan', $value);
                } elseif ($field === 'tagihanDesa') {
                    // Filter berdasarkan desa pelanggan
                    $query->whereHas('pelanggan', function ($q) use ($value) {
                        $q->where('pelangganDesa', $value);
                    });
                } elseif ($field === 'tagihanTahun') {
                    // Filter berdasarkan tahun tagihan
                    $query->where('tagihanTahun', $value);
                } elseif ($field === 'pelangganRt') {
                    // Filter berdasarkan rt pelanggan
                    $query->whereHas('pelanggan', function ($q) use ($value) {
                        $q->where('pelangganRt', $value);
                    });
                } elseif ($field === 'pelangganRw') {
                    // Filter berdasarkan rw pelanggan
                    $query->whereHas('pelanggan', function ($q) use ($value) {
                        $q->where('pelangganRw', $value);
                    });
                } else {
                    // Filter untuk kolom lain di tabel tagihans
                    $query->where($field, 'like', '%' . $value . '%');
                }
            }
        }

        $data = $query->get();
        $data->transform(function($item) {
            $pelanggan = $item->pelanggan;
            $item->pelangganNama = $pelanggan->pelangganNama;
            $item->pelangganAlamat = $pelanggan->pelangganDesa;
            $item->pelangganRTRW = $pelanggan->pelangganRt . ' / ' . $pelanggan->pelangganRw;
            $item->tagihanPenggunaan = $item->tagihanMAkhir - $item->tagihanMAwal;
            $item->tagihanTerbit = $item->tagihanBulan . ' - ' . $item->tagihanTahun;
            return $item;
        });

        // Generate PDF
        $pdf = Pdf::loadView('laporans.pdf.index', ['data' => $data, 'title' => $this->title, 'grid' => $this->grid]);

        // Simpan PDF ke folder storage
        $fileName = 'laporan-tagihan-' . time() . '.pdf';
        Storage::disk('public')->put('exports/tagihan/' . $fileName, $pdf->output());

        $fileUrl = asset('storage/exports/tagihan/' . $fileName);


        // Return URL untuk file yang diunduh
        return response()->json([
            'status' => 'success',
            'url' => $fileUrl,
        ]);
    }
}
