<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     * 
     * Allow receiver_id to be NULL for broadcast messages to all admins
     */
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // Make receiver_id nullable for broadcast messages
            $table->uuid('receiver_id')->nullable()->change();
        });

        // Update existing admin chat messages that have specific receiver_id
        // to NULL so all admins can see them (only for customer -> admin messages)
        // We identify customer messages by checking if sender is NOT in users table as admin
        DB::statement("
            UPDATE messages 
            SET receiver_id = NULL 
            WHERE chat_type = 'admin' 
            AND sender_id IN (SELECT id FROM pelanggans)
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Get the first admin to restore receiver_id
        $adminId = DB::table('users')
            ->whereIn('role', ['administrator', 'admin'])
            ->value('id');

        if ($adminId) {
            DB::statement("
                UPDATE messages 
                SET receiver_id = ? 
                WHERE chat_type = 'admin' AND receiver_id IS NULL
            ", [$adminId]);
        }

        Schema::table('messages', function (Blueprint $table) {
            // Note: This might fail if there are still NULL values
            // The update above should handle it
        });
    }
};
