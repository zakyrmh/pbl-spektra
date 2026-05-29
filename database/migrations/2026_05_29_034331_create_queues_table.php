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
            $table->string('queue_number'); // Contoh: A-012
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->onDelete('cascade'); // Nullable jika pure Walk-in tanpa akun
            $table->foreignId('loket_id')->constrained('lokets')->onDelete('cascade'); // Loket tempat dilayani
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->enum('status', ['Menunggu Dipanggil', 'Dipanggil', 'Selesai', 'Lewat']);
            $table->timestamp('started_at')->nullable(); // Waktu mulai dipanggil
            $table->timestamp('ended_at')->nullable(); // Waktu klik selesai
            $table->softDeletes();
            $table->timestamps(); // created_at sebagai waktu tiket dicetak di FO
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
