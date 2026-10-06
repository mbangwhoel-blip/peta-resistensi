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
        Schema::create('lokasi_koleksi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('instansi_id')->nullable()->constrained('instansi')->nullOnDelete();
            $table->foreignId('desa_id')->nullable()->constrained('wilayah')->nullOnDelete();
            $table->string('nama', 150);
            $table->text('alamat_dusun')->nullable();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('tipe_habitat', 30)->default('permukiman'); // permukiman|pelabuhan|bandara|perkotaan|lainnya
            $table->boolean('aktif')->default(true);
            $table->softDeletesTz();
            $table->timestampsTz();

            $table->index(['latitude', 'longitude']);
            $table->index('desa_id');
            $table->index('instansi_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lokasi_koleksi');
    }
};
