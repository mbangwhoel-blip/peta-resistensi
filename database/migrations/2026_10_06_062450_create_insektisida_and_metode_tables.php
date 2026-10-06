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
        Schema::create('golongan_insektisida', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100)->unique(); // piretroid|organofosfat|karbamat|organoklorin|igr|biolarvasida
            $table->timestampsTz();
        });

        Schema::create('insektisida', function (Blueprint $table) {
            $table->id();
            $table->foreignId('golongan_id')->constrained('golongan_insektisida')->cascadeOnDelete();
            $table->string('nama', 100)->unique();
            $table->string('bahan_aktif', 100)->nullable();
            $table->string('peruntukan', 20)->default('keduanya'); // dewasa|larva|keduanya
            $table->decimal('konsentrasi_diskriminasi', 8, 4)->nullable();
            $table->string('satuan_konsentrasi', 20)->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestampsTz();
        });

        Schema::create('metode', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100)->unique();
            $table->string('jenis', 30)->default('kerentanan'); // kerentanan|molekuler|biokimia
            $table->string('stadium_sasaran', 20)->default('keduanya'); // dewasa|larva|keduanya
            $table->string('rujukan_pedoman', 150)->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metode');
        Schema::dropIfExists('insektisida');
        Schema::dropIfExists('golongan_insektisida');
    }
};
