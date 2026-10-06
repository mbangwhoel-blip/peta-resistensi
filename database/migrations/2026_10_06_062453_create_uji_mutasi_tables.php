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
        Schema::create('uji_mutasi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('instansi_id')->nullable()->constrained('instansi')->nullOnDelete();
            $table->foreignUuid('lokasi_koleksi_id')->constrained('lokasi_koleksi')->cascadeOnDelete();
            $table->foreignId('spesies_id')->constrained('spesies');
            $table->foreignId('metode_id')->constrained('metode');
            $table->foreignUuid('petugas_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('tanggal_koleksi');
            $table->date('tanggal_analisis');
            $table->integer('jumlah_sampel')->default(0);
            $table->string('status_data', 25)->default('draft'); // draft|diajukan|diverifikasi|terpublikasi|ditolak
            $table->text('catatan')->nullable();
            $table->timestampTz('published_at')->nullable();
            $table->softDeletesTz();
            $table->timestampsTz();

            $table->index('status_data');
            $table->index('tanggal_analisis');
            $table->index(['spesies_id', 'metode_id']);
            $table->index(['lokasi_koleksi_id', 'status_data']);
        });

        Schema::create('uji_mutasi_marker', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('uji_mutasi_id')->constrained('uji_mutasi')->cascadeOnDelete();
            $table->string('gen', 50); // VGSC|Ace-1|lainnya
            $table->string('mutasi', 50); // V1016G|F1534C|S989P|G119S
            $table->integer('jumlah_rr')->default(0);
            $table->integer('jumlah_rs')->default(0);
            $table->integer('jumlah_ss')->default(0);
            $table->decimal('frekuensi_alel_mutan', 6, 4)->nullable();
            $table->timestampsTz();

            $table->index(['uji_mutasi_id', 'gen', 'mutasi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uji_mutasi_marker');
        Schema::dropIfExists('uji_mutasi');
    }
};
