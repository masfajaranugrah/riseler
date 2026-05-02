<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('pelanggans', 'kena_ppn')) {
            Schema::table('pelanggans', function (Blueprint $table) {
                $table->boolean('kena_ppn')->default(true)->after('nomer_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('pelanggans', 'kena_ppn')) {
            Schema::table('pelanggans', function (Blueprint $table) {
                $table->dropColumn('kena_ppn');
            });
        }
    }
};

