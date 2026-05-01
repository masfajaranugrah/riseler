<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('laporan_kabels')) {
            return;
        }

        if (! Schema::hasColumn('laporan_kabels', 'wilayah')) {
            Schema::table('laporan_kabels', function (Blueprint $table) {
                $table->string('wilayah')->nullable()->after('nama_pelanggan');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('laporan_kabels')) {
            return;
        }

        if (Schema::hasColumn('laporan_kabels', 'wilayah')) {
            Schema::table('laporan_kabels', function (Blueprint $table) {
                $table->dropColumn('wilayah');
            });
        }
    }
};

