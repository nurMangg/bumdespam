<?php

namespace App\Imports;

use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\Roles;
use App\Models\Tagihan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;

class DataImport implements ToModel, WithStartRow, SkipsOnFailure, WithHeadingRow
{
    use SkipsFailures;

    public function startRow(): int
    {
        return 2;
    }

    public function model(array $row)
    {
        $user = User::create([
            'name' => $row['nama'],
            'username' => strtolower($this->generateUniqueCode()),
            'password' => Hash::make('password'),
            'userRoleId' => Roles::where('roleName', 'pelanggan')->first()->roleId
        ]);

        $pelanggan = Pelanggan::create([
            'pelangganKode' => $this->generateUniqueCode(),
            'pelangganNama' => $row['nama'],
            'pelangganPhone' => $row['phone'],
            'pelangganRt' => $row['rt'],
            'pelangganRw' => $row['rw'],
            'pelangganGolonganId' => $row['golongan_id'],
            'pelangganUserId' => $user->id
        ]);

        $tagihan = Tagihan::create([
            'tagihanKode' => $this->generateUniqueCodeTagihan(),
            'tagihanPelangganId' => $pelanggan->pelangganId,
            'tagihanBulan' => $row['bulan'],
            'tagihanTahun' => $row['tahun'],
            'tagihanInfoTarif' => Pelanggan::where('pelangganId', $pelanggan->pelangganId)->first()->golongan->golonganTarif,
            'tagihanInfoAbonemen' => $row['abonemen'],
            'tagihanMAwal' => $row['m_awal'],
            'tagihanMAkhir' => $row['m_akhir'],
            'tagihanUserId' => Auth::user()->id, // Changed from Auth::user()->id
            'tagihanTanggal' => date('Y-m-d'),
            'tagihanStatus' => "Belum Lunas",
        ]);

        return Pembayaran::create([ // Changed from returning new Pembayaran
            'pembayaranTagihanId' => $tagihan->tagihanId,
            'pembayaranJumlah' => (($row['m_akhir'] - $row['m_awal']) * $tagihan->tagihanInfoTarif),
            'pembayaranStatus' => 'Belum Lunas'
        ]);
    }

    public function generateUniqueCode(): string
    {
        $pelangganCount = Pelanggan::count();

        $pelangganPart = str_pad($pelangganCount + 1, 4, '0', STR_PAD_LEFT);

        return "PAM{$pelangganPart}";
    }

    public function generateUniqueCodeTagihan(): string
    {
        $pelangganCount = Tagihan::count();

        $pelangganPart = str_pad($pelangganCount + 1, 4, '0', STR_PAD_LEFT);

        return "TPAM{$pelangganPart}";
    }
}
