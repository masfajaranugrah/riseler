<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('laporan_kabels')) {
            return;
        }

        Schema::create('laporan_kabels', function (Blueprint $table) {
            $table->id();
            $table->string('nomer')->nullable();
            $table->string('nama_pelanggan');
            $table->text('alamat');
            $table->decimal('tarikan_meter', 10, 2)->default(0);
            $table->enum('jenis_kabel', ['1c', '4c', '12c']);
            $table->unsignedInteger('sisi_core');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_kabels');
    }
};
