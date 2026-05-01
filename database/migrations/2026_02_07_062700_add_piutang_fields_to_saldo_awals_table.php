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
        Schema::table('saldo_awals', function (Blueprint $table) {
            // Tambah kolom pemasukan jika belum ada
            if (!Schema::hasColumn('saldo_awals', 'pemasukan_registrasi')) {
                $table->decimal('pemasukan_registrasi', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('saldo_awals', 'pemasukan_dedicated_potongan')) {
                $table->decimal('pemasukan_dedicated_potongan', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('saldo_awals', 'pemasukan_homenet_kotor')) {
                $table->decimal('pemasukan_homenet_kotor', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('saldo_awals', 'pemasukan_homenet_potongan')) {
                $table->decimal('pemasukan_homenet_potongan', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('saldo_awals', 'pemasukan_homenet_bersih')) {
                $table->decimal('pemasukan_homenet_bersih', 15, 2)->default(0);
            }
            
            // Tambah kolom piutang jika belum ada
            if (!Schema::hasColumn('saldo_awals', 'piutang_dedicated')) {
                $table->decimal('piutang_dedicated', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('saldo_awals', 'piutang_homenet')) {
                $table->decimal('piutang_homenet', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('saldo_awals', 'piutang_bulan_sebelumnya')) {
                $table->decimal('piutang_bulan_sebelumnya', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('saldo_awals', 'piutang_periode_sebelumnya')) {
                $table->decimal('piutang_periode_sebelumnya', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('saldo_awals', 'piutang_tahun_lalu')) {
                $table->decimal('piutang_tahun_lalu', 15, 2)->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('saldo_awals', function (Blueprint $table) {
            $columns = [
                'pemasukan_registrasi',
                'pemasukan_dedicated_potongan',
                'pemasukan_homenet_kotor',
                'pemasukan_homenet_potongan',
                'pemasukan_homenet_bersih',
                'piutang_dedicated',
                'piutang_homenet',
                'piutang_bulan_sebelumnya',
                'piutang_periode_sebelumnya',
                'piutang_tahun_lalu',
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('saldo_awals', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
