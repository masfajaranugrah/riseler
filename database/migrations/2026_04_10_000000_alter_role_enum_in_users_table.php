<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify ENUM in MySQL without dropping data
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('administrator', 'admin', 'logistic', 'marketing', 'customer_service', 'team', 'customer', 'karyawan', 'directur') DEFAULT 'team'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to the original ENUM (if strictly necessary, but can cause truncation issues if new roles exist)
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('administrator', 'admin', 'marketing', 'customer_service', 'team', 'customer', 'karyawan') DEFAULT 'team'");
    }
};
