<?php

namespace Database\Seeders;

use App\Models\Bulan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BulanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Bulan::insert([
            [
                'bulan' => 'Januari'
            ],
            [
                'bulan' => 'Februari'
            ],
            [
                'bulan' => 'Maret'
            ],
            [
                'bulan' => 'April'
            ],
            [
                'bulan' => 'Mei'
            ],
            [
                'bulan' => 'Juni'
            ],
            [
                'bulan' => 'Juli'
            ],
            [
                'bulan' => 'Agustus'
            ],
            [
                'bulan' => 'September'
            ],
            [
                'bulan' => 'Oktober'
            ],
            [
                'bulan' => 'November'
            ],
            [
                'bulan' => 'Desember'
            ]
            ]);
    }
}
