<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Counter;
use App\Models\Queue;
use App\Models\Service;
use App\Models\User;
use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class QueueHistorySeeder extends Seeder
{
    public function run(): void
    {
        $today = Carbon::today();

        // 1. Ambil loket dan layanan Disdukcapil
        $counter = Counter::where('name', 'like', '%Loket 01%')->first();
        if (! $counter) {
            return;
        }

        $service = Service::where('department_id', $counter->department_id)->first();
        if (! $service) {
            return;
        }

        // 2. Ambil user pengunjung
        $customer = User::where('role', 'pengunjung')->first();
        if (! $customer) {
            return;
        }

        // 3. Buat beberapa data Completed (Selesai Dilayani)
        $completedVisitors = [
            ['name' => 'Ahmad Roni', 'nik' => '1373021508940001', 'purpose' => 'Cetak KTP-el Rusak'],
            ['name' => 'Siti Aminah', 'nik' => '1373021508940002', 'purpose' => 'Ubah Alamat KK'],
            ['name' => 'Hendra Wijaya', 'nik' => '1373021508940003', 'purpose' => 'Akta Anak Baru Lahir'],
        ];

        $i = 1;
        foreach ($completedVisitors as $vData) {
            $visitor = Visitor::create([
                'name' => $vData['name'],
                'nik' => $vData['nik'],
                'phone' => '0852'.rand(10000000, 99999999),
                'purpose' => $vData['purpose'],
            ]);

            $queueNum = $counter->department->inisial.'-'.str_pad((string) $i, 3, '0', STR_PAD_LEFT);
            $calledTime = $today->copy()->setTime(8, 30 + ($i * 15));
            $completedTime = $calledTime->copy()->addMinutes(rand(8, 14));

            Queue::create([
                'visitor_id' => $visitor->id,
                'counter_id' => $counter->id,
                'service_id' => $service->id,
                'queue_number' => $queueNum,
                'status' => 'Completed',
                'called_at' => $calledTime,
                'completed_at' => $completedTime,
                'queue_date' => $today,
                'created_at' => $calledTime->copy()->subMinutes(rand(10, 30)),
            ]);
            $i++;
        }

        // 4. Buat data Skipped (Terlewat)
        $skippedVisitors = [
            ['name' => 'Bambang Hartono', 'nik' => '1373021811980008', 'purpose' => 'Cetak KTP Hilang'],
            ['name' => 'Dewi Lestari', 'nik' => '1373014512960005', 'purpose' => 'Pembaruan KK'],
        ];

        foreach ($skippedVisitors as $vData) {
            $visitor = Visitor::create([
                'name' => $vData['name'],
                'nik' => $vData['nik'],
                'phone' => '0813'.rand(10000000, 99999999),
                'purpose' => $vData['purpose'],
            ]);

            $queueNum = $counter->department->inisial.'-'.str_pad((string) $i, 3, '0', STR_PAD_LEFT);
            $calledTime = $today->copy()->setTime(9, 30 + ($i * 15));

            Queue::create([
                'visitor_id' => $visitor->id,
                'counter_id' => $counter->id,
                'service_id' => $service->id,
                'queue_number' => $queueNum,
                'status' => 'Skipped',
                'called_at' => $calledTime,
                'completed_at' => $calledTime->copy()->addMinutes(2), // terlewat terdeteksi cepat
                'queue_date' => $today,
                'created_at' => $calledTime->copy()->subMinutes(25),
            ]);
            $i++;
        }

        // 5. Buat data Serving (Sedang Dilayani)
        $servingVisitor = Visitor::create([
            'name' => 'Ronaldo Saputra',
            'nik' => '1373021811980004',
            'phone' => '082199998888',
            'purpose' => 'Cetak KTP Baru',
        ]);

        $queueNum = $counter->department->inisial.'-'.str_pad((string) $i, 3, '0', STR_PAD_LEFT);
        $calledTime = now()->subMinutes(3); // Sedang dilayani dari 3 menit yang lalu

        Queue::create([
            'visitor_id' => $servingVisitor->id,
            'counter_id' => $counter->id,
            'service_id' => $service->id,
            'queue_number' => $queueNum,
            'status' => 'Serving',
            'called_at' => $calledTime,
            'queue_date' => $today,
            'created_at' => $calledTime->copy()->subMinutes(15),
        ]);
        $i++;

        // 6. Buat beberapa data Waiting (Menunggu)
        $waitingList = [
            ['name' => 'Lia Herlina', 'nik' => '1373014512960002', 'purpose' => 'Pecah KK Mandiri'],
            ['name' => 'Novianti', 'nik' => '1373026605990001', 'purpose' => 'Akta Kelahiran Anak Kedua'],
            ['name' => 'Arief Rahman', 'nik' => '1373030910970005', 'purpose' => 'Ubah Nama KK'],
        ];

        foreach ($waitingList as $vData) {
            $visitor = Visitor::create([
                'name' => $vData['name'],
                'nik' => $vData['nik'],
                'phone' => '0878'.rand(10000000, 99999999),
                'purpose' => $vData['purpose'],
            ]);

            $queueNum = $counter->department->inisial.'-'.str_pad((string) $i, 3, '0', STR_PAD_LEFT);

            Queue::create([
                'visitor_id' => $visitor->id,
                'counter_id' => $counter->id,
                'service_id' => $service->id,
                'queue_number' => $queueNum,
                'status' => 'Waiting',
                'queue_date' => $today,
                'created_at' => now()->subMinutes(10 - $i),
            ]);
            $i++;
        }
    }
}
