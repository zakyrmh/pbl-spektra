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
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->onDelete('set null');
            $table->foreignId('visitor_id')->nullable()->constrained('visitors')->onDelete('set null');
            $table->foreignId('counter_id')->constrained('counters')->onDelete('cascade');
            $table->foreignId('service_id')->nullable()->constrained('services')->onDelete('cascade'); // Di AGENT.md, service_id bisa berelasi untuk mempermudah.
            $table->string('queue_number', 12);
            $table->enum('status', ['Waiting', 'Serving', 'Completed', 'Skipped'])->default('Waiting');
            $table->timestamp('called_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->date('queue_date');
            $table->timestamps();

            // Constraint: queue_number unik per counter_id per queue_date
            $table->unique(['counter_id', 'queue_date', 'queue_number']);
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
