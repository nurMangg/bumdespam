<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Golongan extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Golongan::insert([
            [
                'golonganNama' => 'Rumah Tangga',
                'golonganTarif' => 1350,
                'golonganDenda' => 5000,
                'golonganStatus' => 'Aktif',
            ],
            [
                'golonganNama' => 'Niaga',
                'golonganTarif' => 2500,
                'golonganDenda' => 10000,
                'golonganStatus' => 'Aktif',
            ],
            [
                'golonganNama' => 'Instansi',
                'golonganTarif' => 5000,
                'golonganDenda' => 20000,
                'golonganStatus' => 'Aktif',
            ],
        ]);
    }
}
