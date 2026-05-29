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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // Admin FO yang generate
            $table->string('title');
            $table->date('start_date');
            $table->date('end_date');
            $table->json('data_summary'); // Menyimpan rekap total & per loket dalam format JSON
            $table->enum('status', ['Belum Dikirim', 'Terkirim']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
