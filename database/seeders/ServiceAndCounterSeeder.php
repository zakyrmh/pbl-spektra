<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Counter;
use App\Models\Department;
use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceAndCounterSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil Department
        $ddk = Department::where('inisial', 'DDK')->first();
        $smst = Department::where('inisial', 'SMST')->first();
        $dptk = Department::where('inisial', 'DPTK')->first();
        $fo = Department::where('inisial', 'FO')->first();

        // 2. Buat Services jika department ada
        if ($ddk) {
            $s1 = Service::updateOrCreate(
                ['department_id' => $ddk->id, 'name' => 'Cetak KTP-el'],
                ['description' => 'Pelayanan pencetakan Kartu Tanda Penduduk Elektronik baru atau pengganti karena rusak/hilang.']
            );
            $s2 = Service::updateOrCreate(
                ['department_id' => $ddk->id, 'name' => 'Pembuatan Kartu Keluarga (KK)'],
                ['description' => 'Pelayanan pembuatan KK baru, perubahan data KK, atau pengganti KK rusak/hilang.']
            );
            $s3 = Service::updateOrCreate(
                ['department_id' => $ddk->id, 'name' => 'Penerbitan Akta Kelahiran'],
                ['description' => 'Pelayanan pencatatan sipil penerbitan Akta Kelahiran anak baru.']
            );

            // Buat Counter untuk DDK
            $c1 = Counter::updateOrCreate(
                ['department_id' => $ddk->id, 'name' => 'Loket 01 - Kependudukan'],
                ['location' => 'Lantai 1, Ruang Utama', 'status' => 'aktif']
            );
            $c2 = Counter::updateOrCreate(
                ['department_id' => $ddk->id, 'name' => 'Loket 02 - Pencatatan Sipil'],
                ['location' => 'Lantai 1, Ruang Utama', 'status' => 'aktif']
            );

            // Hubungkan counter ke service
            $c1->services()->sync([$s1->id, $s2->id]);
            $c2->services()->sync([$s3->id]);
        }

        if ($smst) {
            $s4 = Service::updateOrCreate(
                ['department_id' => $smst->id, 'name' => 'Pembayaran Pajak Tahunan'],
                ['description' => 'Pelayanan pembayaran pajak tahunan kendaraan bermotor (PKB) daerah.']
            );

            $c3 = Counter::updateOrCreate(
                ['department_id' => $smst->id, 'name' => 'Loket 03 - Samsat'],
                ['location' => 'Lantai 1, Sayap Kanan', 'status' => 'aktif']
            );

            $c3->services()->sync([$s4->id]);
        }

        if ($dptk) {
            $s5 = Service::updateOrCreate(
                ['department_id' => $dptk->id, 'name' => 'Kartu Pencari Kerja (AK-1)'],
                ['description' => 'Pelayanan pembuatan Kartu Kuning AK-1 bagi pencari kerja lokal Sawahlunto.']
            );

            $c4 = Counter::updateOrCreate(
                ['department_id' => $dptk->id, 'name' => 'Loket 04 - Ketenagakerjaan'],
                ['location' => 'Lantai 1, Sayap Kiri', 'status' => 'aktif']
            );

            $c4->services()->sync([$s5->id]);
        }

        // Tambah Front Office Counter
        if ($fo) {
            $sFo = Service::updateOrCreate(
                ['department_id' => $fo->id, 'name' => 'Informasi & Pengaduan'],
                ['description' => 'Pusat layanan informasi terpadu dan penanganan pengaduan.']
            );

            $cFo = Counter::updateOrCreate(
                ['department_id' => $fo->id, 'name' => 'Loket FO - Informasi'],
                ['location' => 'Lobby Depan Utama', 'status' => 'aktif']
            );

            $cFo->services()->sync([$sFo->id]);
        }
    }
}
