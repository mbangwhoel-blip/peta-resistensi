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
        Schema::create('spesies', function (Blueprint $table) {
            $table->id();
            $table->string('nama_ilmiah', 100)->unique();
            $table->string('nama_umum', 100)->nullable();
            $table->string('genus', 50)->index();
            $table->string('vektor_penyakit', 150)->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestampsTz();
        });

        Schema::create('strain_rujukan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spesies_id')->constrained('spesies')->cascadeOnDelete();
            $table->string('nama', 100);
            $table->text('keterangan')->nullable();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('strain_rujukan');
        Schema::dropIfExists('spesies');
    }
};
