<?php

namespace App\Imports;

use App\Models\Pelanggan;
use App\Models\Roles;
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
        $user = User::create([
            'name' => $row['nama'],
            'email' => strtolower($this->generateUniqueCode()),
            'password' => Hash::make('password'),
            'userRoleId' => Roles::where('roleName', 'pelanggan')->first()->roleId
        ]);

        return new Pelanggan([
            'pelangganKode' => $this->generateUniqueCode(),
            'pelangganNama' => $row['nama'],
            'pelangganPhone' => $row['phone'],
            'pelangganDesa' => $row['desa'],
            'pelangganRt' => $row['rt'],
            'pelangganRw' => $row['rw'],
            'pelangganGolonganId' => $row['golongan_id'],
            'pelangganStatus' => $row['status'],
            'pelangganUserId' => $user->id
        ]);
    }

    public function generateUniqueCode(): string
    {
        $pelangganCount = Pelanggan::count();

        $pelangganPart = str_pad($pelangganCount + 1, 4, '0', STR_PAD_LEFT);

        return "PAM{$pelangganPart}";
    }
}

