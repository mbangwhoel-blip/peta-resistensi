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
        Schema::create('audit_log', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event', 30); // created|updated|deleted|login
            $table->string('auditable_type', 100)->nullable();
            $table->string('auditable_id', 100)->nullable();
            $table->jsonb('nilai_lama')->nullable();
            $table->jsonb('nilai_baru')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestampTz('created_at');

            $table->index(['auditable_type', 'auditable_id']);
            $table->index(['user_id', 'created_at']);
        });

        Schema::create('pengaturan_status', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('diubah_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->string('kunci', 100)->unique();
            $table->jsonb('nilai');
            $table->timestampTz('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturan_status');
        Schema::dropIfExists('audit_log');
    }
};
