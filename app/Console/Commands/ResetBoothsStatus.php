<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\Department;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

#[Signature('app:reset-booths-status')]
#[Description('Reset seluruh status loket di cache ke nonaktif')]
class ResetBoothsStatus extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $departments = Department::all();
        $count = $departments->count();

        if ($count === 0) {
            $this->info('Tidak ada loket/departemen untuk di-reset.');

            return 0;
        }

        $this->info("Menemukan {$count} loket/departemen. Memulai proses reset status loket harian...");

        foreach ($departments as $dept) {
            // Reset status di cache ke nonaktif
            Cache::put("loket_status_{$dept->id}", 'nonaktif', now()->addDay());

            // Nonaktifkan status buka di database
            $dept->is_open = false;
            $dept->save();

            // Catat activity log audit harian
            ActivityLog::record(
                action: 'AUTO_RESET_COUNTER_STATUS',
                modelType: 'Department',
                modelId: $dept->id,
                description: "Sistem otomatis mereset status loket instansi '{$dept->name}' menjadi 'nonaktif' pada penutupan harian.",
                actorUserId: null
            );
        }

        $this->info("Berhasil mereset status {$count} loket gerai ke nonaktif.");
        Log::info("AUTO_RESET: Status {$count} loket gerai berhasil di-reset ke nonaktif.");

        return 0;
    }
}
