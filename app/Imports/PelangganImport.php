<?php

namespace App\Imports;

use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;

class PelangganImport implements ToModel, WithStartRow, SkipsOnFailure, WithHeadingRow
{
    use SkipsFailures;

    public function startRow(): int
    {
        return 2;
    }

    public function model(array $row)
    {
        User::create([
            'name' => $row['nama'],
            'email' => $row['email'],
            'password' => Hash::make('password'),
            'userRoleId' => 2
        ]);

        return new Pelanggan([
            'pelangganKode' => $this->generateUniqueCode(),
            'pelangganNama' => $row['nama'],
            'pelangganEmail' => $row['email'],
            'pelangganPhone' => $row['phone'],
            'pelangganAlamat' => $row['alamat'],
            'pelangganDesa' => $row['desa'],
            'pelangganRt' => $row['rt'],
            'pelangganRw' => $row['rw'],
            'pelangganGolonganId' => $row['golongan_id'],
            'pelangganStatus' => $row['status'],
            'pelangganUserId' => Auth::user()->id
        ]);
    }

    public function generateUniqueCode(): string
    {
        $date = date('ym');
        $pelangganCount = Pelanggan::whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->count();

        $pelangganPart = str_pad($pelangganCount + 1, 3, '0', STR_PAD_LEFT);

        return "PAM{$date}{$pelangganPart}";
    }
}

