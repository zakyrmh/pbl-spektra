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
        Schema::table('queues', function (Blueprint $table) {
            $table->foreignId('parent_queue_id')
                ->nullable()
                ->after('user_id')
                ->constrained('queues')
                ->nullOnDelete();
            $table->json('next_department_ids')->nullable()->after('department_id');
            $table->unsignedInteger('sequence_order')->default(1)->after('queue_number');
            $table->boolean('is_waterfall_forwarded')->default(false)->after('is_priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('queues', function (Blueprint $table) {
            $table->dropForeign(['parent_queue_id']);
            $table->dropColumn([
                'parent_queue_id',
                'next_department_ids',
                'sequence_order',
                'is_waterfall_forwarded',
            ]);
        });
    }
};
