<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gaji', function (Blueprint $table) {
            $table->uuid('id')->primary(); // UUID untuk primary key

            $table->uuid('employee_id'); // UUID foreign key
            $table->foreign('employee_id')
                  ->references('id')
                  ->on('employees')
                  ->onDelete('cascade');

            // Gaji dan tunjangan
            $table->decimal('gaji_pokok', 12, 2)->nullable();
            $table->decimal('tunj_jabatan', 12, 2)->nullable();
            $table->decimal('tunj_fungsional', 12, 2)->nullable();
            $table->decimal('transport', 12, 2)->nullable();
            $table->decimal('makan', 12, 2)->nullable();
            $table->decimal('tunj1', 12, 2)->nullable();
            $table->decimal('tunj2', 12, 2)->nullable();
            $table->decimal('tunj3', 12, 2)->nullable();
            $table->decimal('tunj_kehadiran', 12, 2)->nullable();
            $table->decimal('lembur', 12, 2)->nullable();

            // Potongan
            $table->decimal('pot_sosial', 12, 2)->nullable();
            $table->decimal('pot_denda', 12, 2)->nullable();
            $table->decimal('pot_koperasi', 12, 2)->nullable();
            $table->decimal('pot_pajak', 12, 2)->nullable();
            $table->decimal('pot_lain', 12, 2)->nullable();

            $table->decimal('total', 14, 2)->nullable();
            $table->decimal('grand_total', 14, 2)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gaji');
    }
};
