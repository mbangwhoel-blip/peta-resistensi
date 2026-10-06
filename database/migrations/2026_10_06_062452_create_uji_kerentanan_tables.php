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
        Schema::create('uji_kerentanan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('instansi_id')->nullable()->constrained('instansi')->nullOnDelete();
            $table->foreignUuid('lokasi_koleksi_id')->constrained('lokasi_koleksi')->cascadeOnDelete();
            $table->foreignId('spesies_id')->constrained('spesies');
            $table->foreignId('insektisida_id')->constrained('insektisida');
            $table->foreignId('metode_id')->constrained('metode');
            $table->foreignId('strain_rujukan_id')->nullable()->constrained('strain_rujukan')->nullOnDelete();
            $table->foreignUuid('petugas_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('tanggal_koleksi');
            $table->date('tanggal_uji');
            $table->string('stadium', 20)->default('dewasa'); // dewasa|larva
            $table->string('generasi', 20)->default('F0'); // F0|F1|lainnya
            $table->string('jenis_sampel', 30)->default('lapangan'); // lapangan|strain_rujukan
            $table->decimal('konsentrasi', 8, 4)->nullable();
            $table->string('satuan_konsentrasi', 20)->nullable();
            $table->integer('durasi_pemaparan_menit')->nullable();
            $table->integer('durasi_pengamatan_jam')->nullable();
            $table->decimal('suhu_c', 5, 2)->nullable();
            $table->decimal('kelembapan_persen', 5, 2)->nullable();
            $table->integer('jumlah_uji_total')->default(0);
            $table->integer('jumlah_mati_total')->default(0);
            $table->decimal('mortalitas_persen', 5, 2)->nullable();
            $table->decimal('mortalitas_kontrol_persen', 5, 2)->nullable();
            $table->decimal('mortalitas_terkoreksi_persen', 5, 2)->nullable();
            $table->decimal('lc50', 8, 4)->nullable();
            $table->decimal('lc95', 8, 4)->nullable();
            $table->decimal('rr50', 8, 4)->nullable();
            $table->decimal('rr95', 8, 4)->nullable();
            $table->string('status_resistensi', 30)->default('belum_cukup_data'); // rentan|kemungkinan_resisten|resisten|resisten_sedang|resisten_tinggi|belum_cukup_data
            $table->boolean('valid')->default(true);
            $table->string('status_data', 25)->default('draft'); // draft|diajukan|diverifikasi|terpublikasi|ditolak
            $table->text('catatan')->nullable();
            $table->timestampTz('published_at')->nullable();
            $table->softDeletesTz();
            $table->timestampsTz();

            $table->index('status_data');
            $table->index('status_resistensi');
            $table->index('tanggal_uji');
            $table->index(['spesies_id', 'insektisida_id', 'metode_id']);
            $table->index(['lokasi_koleksi_id', 'status_data']);
        });

        Schema::create('uji_replikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('uji_kerentanan_id')->constrained('uji_kerentanan')->cascadeOnDelete();
            $table->string('tipe', 20)->default('perlakuan'); // perlakuan|kontrol
            $table->integer('nomor');
            $table->integer('jumlah_uji');
            $table->integer('jumlah_knockdown')->default(0);
            $table->integer('jumlah_mati')->default(0);
            $table->timestampsTz();

            $table->index(['uji_kerentanan_id', 'tipe', 'nomor']);
        });

        Schema::create('uji_konsentrasi', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('uji_kerentanan_id')->constrained('uji_kerentanan')->cascadeOnDelete();
            $table->decimal('konsentrasi', 8, 4);
            $table->string('satuan', 20);
            $table->integer('jumlah_larva');
            $table->integer('jumlah_mati');
            $table->timestampsTz();

            $table->index('uji_kerentanan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uji_konsentrasi');
        Schema::dropIfExists('uji_replikasi');
        Schema::dropIfExists('uji_kerentanan');
    }
};
