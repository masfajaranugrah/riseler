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
        Schema::table('messages', function (Blueprint $table) {
            if (!Schema::hasColumn('messages', 'is_deleted')) {
                $table->boolean('is_deleted')->default(false)->after('is_read');
                $table->index('is_deleted');
            }

            if (!Schema::hasColumn('messages', 'edited_at')) {
                $table->timestamp('edited_at')->nullable()->after('updated_at');
            }

            if (!Schema::hasColumn('messages', 'deleted_at')) {
                $table->timestamp('deleted_at')->nullable()->after('edited_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            if (Schema::hasColumn('messages', 'is_deleted')) {
                $table->dropIndex(['is_deleted']);
                $table->dropColumn('is_deleted');
            }

            if (Schema::hasColumn('messages', 'edited_at')) {
                $table->dropColumn('edited_at');
            }

            if (Schema::hasColumn('messages', 'deleted_at')) {
                $table->dropColumn('deleted_at');
            }
        });
    }
};
