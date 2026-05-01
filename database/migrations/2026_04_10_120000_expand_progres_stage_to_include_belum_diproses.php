<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('pelanggans', 'progres')) {
            return;
        }

        DB::statement("
            ALTER TABLE pelanggans
            MODIFY progres ENUM('Belum Diproses', 'Tarik Kabel', 'Aktivasi', 'Registrasi')
            NULL DEFAULT 'Belum Diproses'
        ");

        DB::table('pelanggans')
            ->whereNull('progres')
            ->orWhere('progres', '')
            ->update(['progres' => 'Belum Diproses']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('pelanggans', 'progres')) {
            return;
        }

        DB::table('pelanggans')
            ->where('progres', 'Belum Diproses')
            ->update(['progres' => null]);

        DB::statement("
            ALTER TABLE pelanggans
            MODIFY progres ENUM('Tarik Kabel', 'Aktivasi', 'Registrasi')
            NULL
        ");
    }
};
