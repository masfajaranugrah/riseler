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

        Schema::table('laporan_kabels', function (Blueprint $table) {
            if (! Schema::hasColumn('laporan_kabels', 'employee_id')) {
                $table->uuid('employee_id')->nullable()->after('wilayah');
            }

            if (! Schema::hasColumn('laporan_kabels', 'keterangan')) {
                $table->text('keterangan')->nullable()->after('sisi_core');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('laporan_kabels')) {
            return;
        }

        Schema::table('laporan_kabels', function (Blueprint $table) {
            if (Schema::hasColumn('laporan_kabels', 'employee_id')) {
                $table->dropColumn('employee_id');
            }

            if (Schema::hasColumn('laporan_kabels', 'keterangan')) {
                $table->dropColumn('keterangan');
            }
        });
    }
};

