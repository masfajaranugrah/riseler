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
        if (!Schema::hasColumn('pelanggans', 'progress_note')) {
            Schema::table('pelanggans', function (Blueprint $table) {
                $table->text('progress_note')->nullable()->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('pelanggans', 'progress_note')) {
            Schema::table('pelanggans', function (Blueprint $table) {
                $table->dropColumn('progress_note');
            });
        }
    }
};
