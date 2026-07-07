<?php

declare(strict_types=1);

use App\Models\ActivityLog;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

uses(RefreshDatabase::class);

test('it resets all booths status in cache to nonaktif and sets is_open to false in database', function () {
    Log::spy();

    // 1. Create departments
    $dept1 = Department::create([
        'name' => 'Layanan A',
        'inisial' => 'LA',
        'nomor_loket' => '01',
        'is_open' => true,
    ]);

    $dept2 = Department::create([
        'name' => 'Layanan B',
        'inisial' => 'LB',
        'nomor_loket' => '02',
        'is_open' => true,
    ]);

    // 2. Set active statuses in Cache
    Cache::put("loket_status_{$dept1->id}", 'aktif', now()->addDay());
    Cache::put("loket_status_{$dept2->id}", 'istirahat', now()->addDay());

    // Assert initial states
    $this->assertTrue($dept1->is_open);
    $this->assertTrue($dept2->is_open);
    $this->assertEquals('aktif', Cache::get("loket_status_{$dept1->id}"));
    $this->assertEquals('istirahat', Cache::get("loket_status_{$dept2->id}"));

    // 3. Run command
    $this->artisan('app:reset-booths-status')
        ->expectsOutput('Menemukan 2 loket/departemen. Memulai proses reset status loket harian...')
        ->expectsOutput('Berhasil mereset status 2 loket gerai ke nonaktif.')
        ->assertExitCode(0);

    // 4. Assert statuses are updated to nonaktif in Cache
    $this->assertEquals('nonaktif', Cache::get("loket_status_{$dept1->id}"));
    $this->assertEquals('nonaktif', Cache::get("loket_status_{$dept2->id}"));

    // 5. Assert is_open is updated to false in Database
    $this->assertFalse($dept1->fresh()->is_open);
    $this->assertFalse($dept2->fresh()->is_open);

    // 6. Assert ActivityLog is recorded
    $this->assertTrue(ActivityLog::where('event', 'AUTO_RESET_COUNTER_STATUS')
        ->where('subject_id', $dept1->id)
        ->exists());
    $this->assertTrue(ActivityLog::where('event', 'AUTO_RESET_COUNTER_STATUS')
        ->where('subject_id', $dept2->id)
        ->exists());

    // 7. Verify Log was recorded
    Log::shouldHaveReceived('info')
        ->once()
        ->with(Mockery::on(fn ($message) => str_contains($message, 'AUTO_RESET: Status 2 loket gerai berhasil di-reset ke nonaktif.')));
});
