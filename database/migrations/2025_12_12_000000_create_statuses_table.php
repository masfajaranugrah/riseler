<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('statuses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('pelanggan_id');
            $table->boolean('is_active')->default(true);
            $table->timestamp('logged_in_at')->nullable();
            $table->timestamps();

            $table->foreign('pelanggan_id')->references('id')->on('pelanggans')->onDelete('cascade');
            $table->unique('pelanggan_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('statuses');
    }
};
