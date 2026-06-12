<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds 'Hold' to the queues.status enum.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE queues MODIFY COLUMN status ENUM('Booked','Checked-In','Serving','Hold','Completed','Skipped','Cancelled') NOT NULL DEFAULT 'Booked'");
    }

    /**
     * Reverse the migrations.
     * Removes 'Hold' from the queues.status enum.
     */
    public function down(): void
    {
        // Move any Hold queues back to Serving before removing the enum value
        DB::statement("UPDATE queues SET status = 'Serving' WHERE status = 'Hold'");

        DB::statement("ALTER TABLE queues MODIFY COLUMN status ENUM('Booked','Checked-In','Serving','Completed','Skipped','Cancelled') NOT NULL DEFAULT 'Booked'");
    }
};
