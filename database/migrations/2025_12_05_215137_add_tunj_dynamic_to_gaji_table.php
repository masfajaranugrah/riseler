<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gaji', function (Blueprint $table) {
            $table->json('tunj_dynamic')->nullable()->after('makan');
            $table->json('tunj_keterangan')->nullable()->after('tunj_dynamic');
        });
    }

    public function down(): void
    {
        Schema::table('gaji', function (Blueprint $table) {
            $table->dropColumn(['tunj_dynamic', 'tunj_keterangan']);
        });
    }
};
