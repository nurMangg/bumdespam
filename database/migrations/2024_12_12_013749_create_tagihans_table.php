<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tagihans', function (Blueprint $table) {
            $table->increments('tagihanId');
            $table->string('tagihanKode');
            $table->unsignedInteger('tagihanPelangganId')->nullable();
            $table->foreign('tagihanPelangganId')
                ->references('pelangganId')
                ->on('mspelanggan')
                ->onDelete('cascade');
            $table->string('tagihanBulan');
            $table->string('tagihanTahun');

            $table->double('tagihanInfoTarif');
            $table->double('tagihanInfoDenda');
            
            $table->double('tagihanMAwal');
            $table->double('tagihanMAkhir');

            $table->integer('tagihanUserId')->nullable();

            $table->date('tagihanTanggal');

            $table->enum('tagihanStatus', ['Lunas', 'Belum Lunas', 'Pending'])->default('Belum Lunas');

            $table->text('tagihanCatatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihans');
    }
};
