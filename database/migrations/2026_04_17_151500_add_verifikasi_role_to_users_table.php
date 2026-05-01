<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('administrator', 'admin', 'logistic', 'marketing', 'customer_service', 'team', 'teknisi', 'koordinator', 'customer', 'karyawan', 'directur', 'verifikasi') DEFAULT 'team'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('administrator', 'admin', 'logistic', 'marketing', 'customer_service', 'team', 'teknisi', 'koordinator', 'customer', 'karyawan', 'directur') DEFAULT 'team'");
    }
};
