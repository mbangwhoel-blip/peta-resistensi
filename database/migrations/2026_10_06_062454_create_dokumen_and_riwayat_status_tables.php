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
        Schema::create('dokumen', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('dokumen_type', 100); // uji_kerentanan|uji_mutasi
            $table->uuid('dokumen_id');
            $table->foreignUuid('pemilik_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama_file', 255);
            $table->string('jenis_dokumen', 50)->nullable();
            $table->string('path', 255);
            $table->bigInteger('ukuran_byte')->nullable();
            $table->timestampTz('uploaded_at');
            $table->timestampsTz();

            $table->index(['dokumen_type', 'dokumen_id']);
        });

        Schema::create('riwayat_status', function (Blueprint $table) {
            $table->id();
            $table->string('statusable_type', 100); // uji_kerentanan|uji_mutasi
            $table->uuid('statusable_id');
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('dari_status', 30)->nullable();
            $table->string('ke_status', 30);
            $table->text('alasan')->nullable();
            $table->timestampTz('created_at');

            $table->index(['statusable_type', 'statusable_id']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_status');
        Schema::dropIfExists('dokumen');
    }
};
