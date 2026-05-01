<?php
// database/migrations/2025_12_13_110926_create_iklans_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('iklans', function (Blueprint $table) {
            // Primary key pakai UUID
            $table->uuid('id')->primary();
            
            $table->string('title');
            $table->text('message');
            $table->string('image')->nullable();
            
            // ? TAMBAH TYPE
            $table->enum('type', ['informasi', 'maintenance', 'iklan'])->default('iklan');
            
            $table->integer('total_sent')->default(0);
            $table->enum('status', ['draft', 'active'])->default('draft');
            $table->timestamp('sent_at')->nullable();
            
            // Foreign key juga UUID
            $table->foreignUuid('created_by')->constrained('users')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iklans');
    }
};
