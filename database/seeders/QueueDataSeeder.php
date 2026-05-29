<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Counter;
use App\Models\Department;
use App\Models\Queue;
use App\Models\Service;
use App\Models\User;
use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class QueueDataSeeder extends Seeder
{
    public function run(): void
    {
        $today = Carbon::today();

        // 1. Get departments
        $ddk = Department::where('inisial', 'DDK')->first();
        $smst = Department::where('inisial', 'SMST')->first();
        $imi = Department::where('inisial', 'IMI')->first();
        $bpjsk = Department::where('inisial', 'BPJSK')->first();
        $dptk = Department::where('inisial', 'DPTK')->first();

        if (! $ddk || ! $smst || ! $imi || ! $bpjsk) {
            return;
        }

        // 2. Create services
        $serviceKtp = Service::updateOrCreate(
            ['department_id' => $ddk->id, 'name' => 'Cetak KTP-el'],
            ['description' => 'Pelayanan pencetakan KTP elektronik baru atau penggantian']
        );
        $serviceKk = Service::updateOrCreate(
            ['department_id' => $ddk->id, 'name' => 'Pembuatan Kartu Keluarga'],
            ['description' => 'Pelayanan pembuatan atau pembaruan KK']
        );
        $serviceMotor = Service::updateOrCreate(
            ['department_id' => $smst->id, 'name' => 'Pajak Tahunan Motor'],
            ['description' => 'Pembayaran pajak kendaraan bermotor tahunan']
        );
        $servicePaspor = Service::updateOrCreate(
            ['department_id' => $imi->id, 'name' => 'Pembuatan Paspor Baru'],
            ['description' => 'Pelayanan pembuatan paspor baru untuk WNI']
        );
        $serviceBpjs = Service::updateOrCreate(
            ['department_id' => $bpjsk->id, 'name' => 'Pendaftaran PPU'],
            ['description' => 'Pendaftaran Pekerja Penerima Upah']
        );

        // 3. Create counters
        $counterDdk1 = Counter::updateOrCreate(
            ['department_id' => $ddk->id, 'name' => 'Loket DDK 01'],
            ['location' => 'Lantai 1 Sayap Kanan']
        );
        $counterDdk2 = Counter::updateOrCreate(
            ['department_id' => $ddk->id, 'name' => 'Loket DDK 02'],
            ['location' => 'Lantai 1 Sayap Kanan']
        );
        $counterSmst = Counter::updateOrCreate(
            ['department_id' => $smst->id, 'name' => 'Loket Samsat 01'],
            ['location' => 'Lantai 1 Tengah']
        );
        $counterImi = Counter::updateOrCreate(
            ['department_id' => $imi->id, 'name' => 'Loket Imigrasi 01'],
            ['location' => 'Lantai 2 Sayap Kiri']
        );
        $counterBpjs = Counter::updateOrCreate(
            ['department_id' => $bpjsk->id, 'name' => 'Loket BPJS 01'],
            ['location' => 'Lantai 1 Sayap Kiri']
        );

        // 4. Create visitors & bookings
        $pengunjung = User::where('role', 'pengunjung')->first();
        if (! $pengunjung) {
            $pengunjung = User::create([
                'name' => 'Budi Santoso',
                'email' => 'pengunjung@example.com',
                'password' => bcrypt('password'),
                'role' => 'pengunjung',
                'nik' => '1372010101900001',
                'no_telp' => '081234567890',
            ]);
        }

        // Create some visitors
        $visitor1 = Visitor::create([
            'name' => 'Rahmat Hidayat',
            'nik' => '1373021408990002',
            'phone' => '081211112222',
            'purpose' => 'Mengurus KTP yang hilang',
        ]);
        $visitor2 = Visitor::create([
            'name' => 'Eko Sulistyo',
            'nik' => '1373010502930005',
            'phone' => '081233334444',
            'purpose' => 'Pajak motor 5 tahunan',
        ]);
        $visitor3 = Visitor::create([
            'name' => 'Siti Aminah',
            'nik' => '1373034907950001',
            'phone' => '081255556666',
            'purpose' => 'Bikin paspor umroh',
        ]);
        $visitor4 = Visitor::create([
            'name' => 'Andi Wijaya',
            'nik' => '1373041508920003',
            'phone' => '081277778888',
            'purpose' => 'Daftar BPJS Kesehatan keluarga',
        ]);
        $visitor5 = Visitor::create([
            'name' => 'Laila Sari',
            'nik' => '1373051609910004',
            'phone' => '081299990000',
            'purpose' => 'Cetak KTP baru rusak',
        ]);

        // Create bookings
        $booking1 = Booking::create([
            'user_id' => $pengunjung->id,
            'service_id' => $serviceKtp->id,
            'booking_code' => (string) Str::uuid(),
            'status' => 'Checked-In',
            'booking_date' => $today,
        ]);

        // 5. Create queues
        // Disdukcapil (DDK) - Lancar (Waiting: 2, Serving: 1, Completed: 3)
        // Let's create Completed queues first to set average waiting time.
        // Waiting time: difference between created_at and called_at (started_at)
        // e.g. 10 mins, 15 mins, 12 mins. Average waiting time around 12 mins.

        // DDK Completed 1 (Created 08:00, Called 08:10, Completed 08:25)
        Queue::create([
            'visitor_id' => $visitor1->id,
            'counter_id' => $counterDdk1->id,
            'service_id' => $serviceKtp->id,
            'queue_number' => 'DDK-001',
            'status' => 'Completed',
            'called_at' => Carbon::now()->subMinutes(120),
            'completed_at' => Carbon::now()->subMinutes(105),
            'queue_date' => $today,
            'created_at' => Carbon::now()->subMinutes(130),
        ]);

        // DDK Completed 2 (Created 08:15, Called 08:30, Completed 08:45)
        Queue::create([
            'visitor_id' => $visitor5->id,
            'counter_id' => $counterDdk1->id,
            'service_id' => $serviceKtp->id,
            'queue_number' => 'DDK-002',
            'status' => 'Completed',
            'called_at' => Carbon::now()->subMinutes(90),
            'completed_at' => Carbon::now()->subMinutes(75),
            'queue_date' => $today,
            'created_at' => Carbon::now()->subMinutes(105),
        ]);

        // DDK Completed 3 (Created 08:40, Called 08:52, Completed 09:10)
        Queue::create([
            'booking_id' => $booking1->id,
            'counter_id' => $counterDdk2->id,
            'service_id' => $serviceKtp->id,
            'queue_number' => 'DDK-003',
            'status' => 'Completed',
            'called_at' => Carbon::now()->subMinutes(60),
            'completed_at' => Carbon::now()->subMinutes(42),
            'queue_date' => $today,
            'created_at' => Carbon::now()->subMinutes(72),
        ]);

        // DDK Serving (Called 10 mins ago)
        Queue::create([
            'visitor_id' => $visitor3->id,
            'counter_id' => $counterDdk1->id,
            'service_id' => $serviceKtp->id,
            'queue_number' => 'DDK-004',
            'status' => 'Serving',
            'called_at' => Carbon::now()->subMinutes(10),
            'queue_date' => $today,
            'created_at' => Carbon::now()->subMinutes(25),
        ]);

        // DDK Waiting 1 (Created 15 mins ago)
        Queue::create([
            'visitor_id' => $visitor1->id,
            'counter_id' => $counterDdk1->id,
            'service_id' => $serviceKk->id,
            'queue_number' => 'DDK-005',
            'status' => 'Waiting',
            'queue_date' => $today,
            'created_at' => Carbon::now()->subMinutes(15),
        ]);

        // DDK Waiting 2 (Created 5 mins ago)
        Queue::create([
            'visitor_id' => $visitor2->id,
            'counter_id' => $counterDdk2->id,
            'service_id' => $serviceKtp->id,
            'queue_number' => 'DDK-006',
            'status' => 'Waiting',
            'queue_date' => $today,
            'created_at' => Carbon::now()->subMinutes(5),
        ]);

        // Samsat (SMST) - Padat (Waiting: 8, Serving: 1, Completed: 2)
        // Let's create completed queues for SMST
        Queue::create([
            'visitor_id' => $visitor2->id,
            'counter_id' => $counterSmst->id,
            'service_id' => $serviceMotor->id,
            'queue_number' => 'SMST-001',
            'status' => 'Completed',
            'called_at' => Carbon::now()->subMinutes(180),
            'completed_at' => Carbon::now()->subMinutes(165),
            'queue_date' => $today,
            'created_at' => Carbon::now()->subMinutes(200), // Waiting time: 20 mins
        ]);

        Queue::create([
            'visitor_id' => $visitor3->id,
            'counter_id' => $counterSmst->id,
            'service_id' => $serviceMotor->id,
            'queue_number' => 'SMST-002',
            'status' => 'Completed',
            'called_at' => Carbon::now()->subMinutes(140),
            'completed_at' => Carbon::now()->subMinutes(125),
            'queue_date' => $today,
            'created_at' => Carbon::now()->subMinutes(158), // Waiting time: 18 mins
        ]);

        // SMST Serving
        Queue::create([
            'visitor_id' => $visitor4->id,
            'counter_id' => $counterSmst->id,
            'service_id' => $serviceMotor->id,
            'queue_number' => 'SMST-003',
            'status' => 'Serving',
            'called_at' => Carbon::now()->subMinutes(8),
            'queue_date' => $today,
            'created_at' => Carbon::now()->subMinutes(30),
        ]);

        // Create 8 Waiting queues for SMST to make it "Padat" (> 5 waiting)
        for ($i = 4; $i <= 11; $i++) {
            Queue::create([
                'visitor_id' => $visitor1->id,
                'counter_id' => $counterSmst->id,
                'service_id' => $serviceMotor->id,
                'queue_number' => 'SMST-'.str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                'status' => 'Waiting',
                'queue_date' => $today,
                'created_at' => Carbon::now()->subMinutes(25 - $i),
            ]);
        }

        // BPJS Kesehatan (BPJSK) - Kosong (Waiting: 0, Serving: 0, Completed: 1)
        Queue::create([
            'visitor_id' => $visitor4->id,
            'counter_id' => $counterBpjs->id,
            'service_id' => $serviceBpjs->id,
            'queue_number' => 'BPJSK-001',
            'status' => 'Completed',
            'called_at' => Carbon::now()->subMinutes(120),
            'completed_at' => Carbon::now()->subMinutes(105),
            'queue_date' => $today,
            'created_at' => Carbon::now()->subMinutes(125), // Waiting time: 5 mins
        ]);

        // Imigrasi (IMI) - Lancar (Waiting: 1, Serving: 1, Completed: 1)
        Queue::create([
            'visitor_id' => $visitor3->id,
            'counter_id' => $counterImi->id,
            'service_id' => $servicePaspor->id,
            'queue_number' => 'IMI-001',
            'status' => 'Completed',
            'called_at' => Carbon::now()->subMinutes(150),
            'completed_at' => Carbon::now()->subMinutes(130),
            'queue_date' => $today,
            'created_at' => Carbon::now()->subMinutes(165), // Waiting time: 15 mins
        ]);

        Queue::create([
            'visitor_id' => $visitor2->id,
            'counter_id' => $counterImi->id,
            'service_id' => $servicePaspor->id,
            'queue_number' => 'IMI-002',
            'status' => 'Serving',
            'called_at' => Carbon::now()->subMinutes(15),
            'queue_date' => $today,
            'created_at' => Carbon::now()->subMinutes(30),
        ]);

        Queue::create([
            'visitor_id' => $visitor5->id,
            'counter_id' => $counterImi->id,
            'service_id' => $servicePaspor->id,
            'queue_number' => 'IMI-003',
            'status' => 'Waiting',
            'queue_date' => $today,
            'created_at' => Carbon::now()->subMinutes(10),
        ]);
    }
}
