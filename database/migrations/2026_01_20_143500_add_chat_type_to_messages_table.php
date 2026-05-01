<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            if (!Schema::hasColumn('messages', 'chat_type')) {
                $table->enum('chat_type', ['cs', 'admin'])->default('cs')->after('receiver_id');
                $table->index('chat_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            if (Schema::hasColumn('messages', 'chat_type')) {
                $table->dropIndex(['chat_type']);
                $table->dropColumn('chat_type');
            }
        });
    }
};
