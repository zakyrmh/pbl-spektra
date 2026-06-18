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
            ['name' => 'Front Office', 'inisial' => 'FO', 'nomor_loket' => '00', 'description' => 'Loket Front Office Terdepan'],
            ['name' => 'DPMPTSPNaker', 'inisial' => 'DPTK', 'nomor_loket' => '01', 'description' => 'Dinas Penanaman Modal, Pelayanan Terpadu Satu Pintu dan Tenaga Kerja'],
            ['name' => 'BNNK', 'inisial' => 'BNNK', 'nomor_loket' => '02', 'description' => 'Badan Narkotika Nasional Kota'],
            ['name' => 'Sawahlunto Siap Kerja', 'inisial' => 'SSK', 'nomor_loket' => '03', 'description' => 'Layanan Ketenagakerjaan Sawahlunto'],
            ['name' => 'ATR/BPN', 'inisial' => 'BPN', 'nomor_loket' => '04', 'description' => 'Agraria dan Tata Ruang/Badan Pertanahan Nasional'],
            ['name' => 'PLN', 'inisial' => 'PLN', 'nomor_loket' => '05', 'description' => 'PT Perusahaan Listrik Negara'],
            ['name' => 'PDAM', 'inisial' => 'PDAM', 'nomor_loket' => '06', 'description' => 'Perusahaan Daerah Air Minum'],
            ['name' => 'Klinik LKPM', 'inisial' => 'KLPM', 'nomor_loket' => '07', 'description' => 'Layanan Laporan Kegiatan Penanaman Modal'],
            ['name' => 'TASPEN', 'inisial' => 'TS', 'nomor_loket' => '08', 'description' => 'Dana Tabungan dan Asuransi Pegawai Negeri'],
            ['name' => 'PT Pos Indonesia', 'inisial' => 'POS', 'nomor_loket' => '09', 'description' => 'Layanan Pos dan Logistik'],
            ['name' => 'LECI', 'inisial' => 'LECI', 'nomor_loket' => '10', 'description' => 'Layanan LECI'],
            ['name' => 'BPJS Kesehatan', 'inisial' => 'BPJSK', 'nomor_loket' => '11', 'description' => 'Badan Penyelenggara Jaminan Sosial Kesehatan'],
            ['name' => 'BPJS Tenaga Kerja', 'inisial' => 'BPJSTK', 'nomor_loket' => '12', 'description' => 'Badan Penyelenggara Jaminan Sosial Ketenagakerjaan'],
            ['name' => 'KP2KP Sawahlunto', 'inisial' => 'KP2KP', 'nomor_loket' => '13', 'description' => 'Kantor Pelayanan, Penyuluhan, dan Konsultasi Perpajakan'],
            ['name' => 'Bank Nagari', 'inisial' => 'BNR', 'nomor_loket' => '14', 'description' => 'PT Bank Pembangunan Daerah Sumatera Barat'],
            ['name' => 'Samsat', 'inisial' => 'SMST', 'nomor_loket' => '15', 'description' => 'Sistem Administrasi Manunggal Satu Atap'],
            ['name' => 'BPKAD', 'inisial' => 'BPKAD', 'nomor_loket' => '16', 'description' => 'Badan Pengelolaan Keuangan dan Aset Daerah'],
            ['name' => 'LPSE', 'inisial' => 'LPSE', 'nomor_loket' => '17', 'description' => 'Layanan Pengadaan Secara Elektronik'],
            ['name' => 'PPID', 'inisial' => 'PPID', 'nomor_loket' => '18', 'description' => 'Pejabat Pengelola Informasi dan Dokumentasi'],
            ['name' => 'Loka POM', 'inisial' => 'LPOM', 'nomor_loket' => '19', 'description' => 'Loka Pengawas Obat dan Makanan'],
            ['name' => 'Klinik Rumah Swadaya', 'inisial' => 'KRS', 'nomor_loket' => '20', 'description' => 'Klinik Rumah Swadaya'],
            ['name' => 'Kemenag Sawahlunto', 'inisial' => 'KMG', 'nomor_loket' => '21', 'description' => 'Kantor Kementerian Agama'],
            ['name' => 'Pengadilan Negeri Sawahlunto', 'inisial' => 'PN', 'nomor_loket' => '22', 'description' => 'Layanan Pengadilan Negeri'],
            ['name' => 'Disdukcapil', 'inisial' => 'DDK', 'nomor_loket' => '23', 'description' => 'Dinas Kependudukan dan Pencatatan Sipil'],
            ['name' => 'SPKT', 'inisial' => 'SPKT', 'nomor_loket' => '24', 'description' => 'Sentra Pelayanan Kepolisian Terpadu'],
            ['name' => 'SKCK', 'inisial' => 'SKCK', 'nomor_loket' => '25', 'description' => 'Surat Keterangan Catatan Kepolisian'],
            ['name' => 'Kejaksaan Negeri Sawahlunto', 'inisial' => 'KJKS', 'nomor_loket' => '26', 'description' => 'Layanan Kejaksaan Negeri'],
            ['name' => 'Kantor Imigrasi Kelas I TPI Padang', 'inisial' => 'IMI', 'nomor_loket' => '27', 'description' => 'Layanan Keimigrasian'],
        ];

        foreach ($departments as $dept) {
            Department::updateOrCreate(['inisial' => $dept['inisial']], $dept);
        }
    }
}
