<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel audit trail untuk mencatat seluruh aktivitas pada sistem MPP Sawahlunto.
     *
     * Struktur:
     * - causer   : siapa yang melakukan aksi (User yang login)
     * - subject  : pada objek apa aksi dilakukan (polymorphic — bisa User, dll.)
     * - event    : nama singkat aksi (created, updated, deleted, dll.)
     * - properties: snapshot before/after data dalam JSON untuk keperluan forensik
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            // Pelaku aksi (nullable untuk aksi sistem/anonim)
            $table->foreignId('causer_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Objek yang dikenai aksi (polymorphic)
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('subject_type')->nullable();
            $table->index(['subject_type', 'subject_id'], 'activity_logs_subject_index');

            // Detail aksi
            $table->string('event', 64);                    // cth: created, updated, deleted, login
            $table->string('description');                  // Teks human-readable
            $table->json('properties')->nullable();         // {before: {...}, after: {...}}

            // Metadata request
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamp('created_at')->useCurrent();

            // Index untuk query cepat per pelaku dan per subjek
            $table->index('causer_id');
            $table->index('event');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
