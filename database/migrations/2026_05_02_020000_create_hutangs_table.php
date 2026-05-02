<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hutangs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_barang');
            $table->bigInteger('jumlah')->default(0);
            $table->text('catatan')->nullable();
            $table->dateTime('tanggal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hutangs');
    }
};
