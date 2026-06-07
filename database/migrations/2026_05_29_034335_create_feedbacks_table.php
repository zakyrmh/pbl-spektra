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
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('queue_id')->unique()->constrained('queues')->onDelete('cascade'); // Unique - Satu ulasan per satu nomor antrean
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Pengunjung yang mengulas
            $table->unsignedTinyInteger('rating'); // Range: 1 - 5
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};
