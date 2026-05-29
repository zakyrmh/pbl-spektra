<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Front Office', 'inisial' => 'FO', 'description' => 'Loket Front Office Terdepan'],
            ['name' => 'DPMPTSPNaker', 'inisial' => 'DPTK', 'description' => 'Dinas Penanaman Modal, Pelayanan Terpadu Satu Pintu dan Tenaga Kerja'],
            ['name' => 'BNNK', 'inisial' => 'BNNK', 'description' => 'Badan Narkotika Nasional Kota'],
            ['name' => 'Sawahlunto Siap Kerja', 'inisial' => 'SSK', 'description' => 'Layanan Ketenagakerjaan Sawahlunto'],
            ['name' => 'ATR/BPN', 'inisial' => 'BPN', 'description' => 'Agraria dan Tata Ruang/Badan Pertanahan Nasional'],
            ['name' => 'PLN', 'inisial' => 'PLN', 'description' => 'PT Perusahaan Listrik Negara'],
            ['name' => 'PDAM', 'inisial' => 'PDAM', 'description' => 'Perusahaan Daerah Air Minum'],
            ['name' => 'Klinik LKPM', 'inisial' => 'KLPM', 'description' => 'Layanan Laporan Kegiatan Penanaman Modal'],
            ['name' => 'TASPEN', 'inisial' => 'TS', 'description' => 'Dana Tabungan dan Asuransi Pegawai Negeri'],
            ['name' => 'PT Pos Indonesia', 'inisial' => 'POS', 'description' => 'Layanan Pos dan Logistik'],
            ['name' => 'LECI', 'inisial' => 'LECI', 'description' => 'Layanan LECI'],
            ['name' => 'BPJS Kesehatan', 'inisial' => 'BPJSK', 'description' => 'Badan Penyelenggara Jaminan Sosial Kesehatan'],
            ['name' => 'BPJS Tenaga Kerja', 'inisial' => 'BPJSTK', 'description' => 'Badan Penyelenggara Jaminan Sosial Ketenagakerjaan'],
            ['name' => 'KP2KP Sawahlunto', 'inisial' => 'KP2KP', 'description' => 'Kantor Pelayanan, Penyuluhan, dan Konsultasi Perpajakan'],
            ['name' => 'Bank Nagari', 'inisial' => 'BNR', 'description' => 'PT Bank Pembangunan Daerah Sumatera Barat'],
            ['name' => 'Samsat', 'inisial' => 'SMST', 'description' => 'Sistem Administrasi Manunggal Satu Atap'],
            ['name' => 'BPKAD', 'inisial' => 'BPKAD', 'description' => 'Badan Pengelolaan Keuangan dan Aset Daerah'],
            ['name' => 'LPSE', 'inisial' => 'LPSE', 'description' => 'Layanan Pengadaan Secara Elektronik'],
            ['name' => 'PPID', 'inisial' => 'PPID', 'description' => 'Pejabat Pengelola Informasi dan Dokumentasi'],
            ['name' => 'Loka POM', 'inisial' => 'LPOM', 'description' => 'Loka Pengawas Obat dan Makanan'],
            ['name' => 'Klinik Rumah Swadaya', 'inisial' => 'KRS', 'description' => 'Klinik Rumah Swadaya'],
            ['name' => 'Kemenag Sawahlunto', 'inisial' => 'KMG', 'description' => 'Kantor Kementerian Agama'],
            ['name' => 'Pengadilan Negeri Sawahlunto', 'inisial' => 'PN', 'description' => 'Layanan Pengadilan Negeri'],
            ['name' => 'Disdukcapil', 'inisial' => 'DDK', 'description' => 'Dinas Kependudukan dan Pencatatan Sipil'],
            ['name' => 'SPKT', 'inisial' => 'SPKT', 'description' => 'Sentra Pelayanan Kepolisian Terpadu'],
            ['name' => 'SKCK', 'inisial' => 'SKCK', 'description' => 'Surat Keterangan Catatan Kepolisian'],
            ['name' => 'Kejaksaan Negeri Sawahlunto', 'inisial' => 'KJKS', 'description' => 'Layanan Kejaksaan Negeri'],
            ['name' => 'Kantor Imigrasi Kelas I TPI Padang', 'inisial' => 'IMI', 'description' => 'Layanan Keimigrasian'],
        ];

        foreach ($departments as $dept) {
            Department::updateOrCreate(['inisial' => $dept['inisial']], $dept);
        }
    }
}
