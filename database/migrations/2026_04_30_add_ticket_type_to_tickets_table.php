<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Tipe tiket: customer = gangguan pelanggan, internal = penugasan internal
            $table->string('ticket_type')->default('customer')->after('id');
            // Judul singkat untuk tiket internal
            $table->string('title')->nullable()->after('ticket_type');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['ticket_type', 'title']);
        });
    }
};
