<?php

namespace App\Http\Controllers\Data;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CekTagihanController extends Controller
{
    public function getTagihanByKodePelanggan(Request $request)
    {
        $kodePelanggan = $request->input('kodePelanggan');
        // dd($kodePelanggan);

        if (!$kodePelanggan) {
            return response()->json(['error' => 'Kode pelanggan tidak ditemukan'], 422);
        }

        $pelanggan = Pelanggan::where('pelangganKode', $kodePelanggan)->first();

        // Query untuk mengambil data tagihan berdasarkan kode pelanggan
        $tagihan = DB::table('tagihans')
            ->join('msbulan', 'tagihans.tagihanBulan', '=', 'msbulan.bulanId')
            ->join('mspelanggan', 'tagihans.tagihanPelangganId', '=', 'mspelanggan.pelangganId')
            ->select(
                'tagihans.tagihanId',
                'mspelanggan.pelangganKode',
                'mspelanggan.pelangganNama',
                'msbulan.bulanNama',
                'tagihans.tagihanTahun',
                'tagihans.tagihanMAwal',
                'tagihans.tagihanMAkhir',
                'tagihans.tagihanInfoTarif',
                'tagihans.tagihanInfoAbonemen',
                DB::raw('(tagihans.tagihanMAkhir - tagihans.tagihanMAwal) * tagihans.tagihanInfoTarif + tagihans.tagihanInfoAbonemen as tagihanTotal'),
                'tagihans.tagihanStatus',
            )
            ->where('mspelanggan.PelangganKode', $kodePelanggan)
            ->whereNull('tagihans.deleted_at')
            ->get();

        return response()->json(['data' => $tagihan]);
    }

    public function index()
    {
        return view('cek-tagihan.index');
    }
}
