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
        Schema::create('mspelanggan', function (Blueprint $table) {
            $table->increments('pelangganId');
            $table->string('pelangganKode');
            $table->string('pelangganNama');
            $table->string('pelangganEmail')->nullable();
            $table->string('pelangganPhone')->nullable();
            $table->text('pelangganAlamat')->nullable();
            $table->string('pelangganDesa')->nullable();
            $table->string('pelangganRt')->nullable();
            $table->string('pelangganRw')->nullable();

            $table->unsignedInteger('pelangganGolonganId')->nullable();
            $table->foreign('pelangganGolonganId')->references('golonganId')->on('msgolongan')->onDelete('cascade');
            
            $table->enum('pelangganStatus', ['Aktif', 'Tidak Aktif'])->default('Aktif');
            $table->integer('pelangganUserId')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mspelanggan');
    }
};
