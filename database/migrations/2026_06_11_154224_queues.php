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
        Schema::create('queues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->foreignId('department_id')
                ->constrained('departments')
                ->onDelete('cascade');
            $table->string('booking_code')->unique();
            $table->text('purpose');
            $table->string('session_name'); // Contoh: 'Sesi 1', 'Sesi 2'
            $table->date('booking_date');
            $table->string('queue_number', 12)->nullable();
            $table->enum('status', ['Booked', 'Checked-In', 'Serving', 'Completed', 'Skipped', 'Cancelled'])->default('Booked');
            $table->string('cancel_reason')->nullable();
            $table->timestamp('checked_in_at')->nullable(); // Klik check-in oleh FO
            $table->timestamp('called_at')->nullable();     // Mulai dilayani oleh Admin Gerai
            $table->timestamp('completed_at')->nullable();  // Selesai dilayani oleh Admin Gerai
            $table->timestamps();
            $table->unique(['department_id', 'booking_date', 'queue_number'], 'idx_dept_date_queue');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queues');
    }
};
