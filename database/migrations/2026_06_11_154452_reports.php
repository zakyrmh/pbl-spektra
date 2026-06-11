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

            $table->foreignId('created_by')
                ->constrained('users')
                ->onDelete('cascade');

            $table->string('title');
            $table->date('start_date');
            $table->date('end_date');
            $table->json('data_summary'); // Menyimpan hasil kalkulasi performa dalam bentuk JSON
            $table->enum('status', ['Belum Dikirim', 'Terkirim'])->default('Belum Dikirim');
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
