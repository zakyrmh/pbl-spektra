<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Schema sesuai AGENT.md §4.1 — Tabel bookings
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('booking_code', 36)->unique(); // UUID v4
            $table->enum('status', ['Pending', 'Checked-In', 'Completed', 'Cancelled'])->default('Pending');
            $table->date('booking_date');
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamps();
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 100);       // e.g. VERIFY_CHECKIN, UPDATE_NIK
            $table->string('model_type', 100);   // e.g. Booking
            $table->unsignedBigInteger('model_id');
            $table->text('description')->nullable();
            $table->timestamp('created_at')->useCurrent();
            // Append-only — tidak ada updated_at (AGENT.md §4.3)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('bookings');
    }
};
