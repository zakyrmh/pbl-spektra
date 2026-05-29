<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['name' => 'DPMPTSPNaker', 'inisial' => 'DPTK', 'description' => 'Dinas Penanaman Modal, Pelayanan Terpadu Satu Pintu dan Tenaga Kerja'],
            ['name' => 'BNNK', 'inisial' => 'BNNK', 'description' => 'Badan Narkotika Nasional Kota Sawahlunto'],
            ['name' => 'Sawahlunto Siap Kerja', 'inisial' => 'SSK', 'description' => 'Layanan Ketenagakerjaan Kota Sawahlunto'],
            ['name' => 'ATR/BPN', 'inisial' => 'BPN', 'description' => 'Badan Pertanahan Nasional / Agraria dan Tata Ruang'],
            ['name' => 'PLN', 'inisial' => 'PLN', 'description' => 'Layanan Pelanggan PT PLN (Persero)'],
            ['name' => 'PDAM', 'inisial' => 'PDAM', 'description' => 'Layanan PDAM Sawahlunto'],
            ['name' => 'Klinik LKPM', 'inisial' => 'KLPM', 'description' => 'Klinik Laporan Kegiatan Penanaman Modal'],
            ['name' => 'TASPEN', 'inisial' => 'TS', 'description' => 'PT TASPEN (Persero) Layanan Pensiunan'],
            ['name' => 'PT Pos Indonesia', 'inisial' => 'POS', 'description' => 'Layanan Pos, Logistik, dan Keuangan'],
            ['name' => 'LECI', 'inisial' => 'LECI', 'description' => 'Layanan LECI Sawahlunto'],
            ['name' => 'BPJS Kesehatan', 'inisial' => 'BPJSK', 'description' => 'Badan Penyelenggara Jaminan Sosial Kesehatan'],
            ['name' => 'BPJS Tenaga Kerja', 'inisial' => 'BPJSTK', 'description' => 'Badan Penyelenggara Jaminan Sosial Ketenagakerjaan'],
            ['name' => 'KP2KP Sawahlunto', 'inisial' => 'KP2KP', 'description' => 'Kantor Pelayanan, Penyuluhan, dan Konsultasi Perpajakan'],
            ['name' => 'Bank Nagari', 'inisial' => 'BNR', 'description' => 'Layanan Perbankan Bank Nagari'],
            ['name' => 'Samsat', 'inisial' => 'SMST', 'description' => 'Sistem Administrasi Manunggal Satu Atap'],
            ['name' => 'BPKAD', 'inisial' => 'BPKAD', 'description' => 'Badan Pengelolaan Keuangan dan Aset Daerah'],
            ['name' => 'LPSE', 'inisial' => 'LPSE', 'description' => 'Layanan Pengadaan Secara Elektronik'],
            ['name' => 'PPID', 'inisial' => 'PPID', 'description' => 'Pejabat Pengelola Informasi dan Dokumentasi'],
            ['name' => 'Loka POM', 'inisial' => 'LPOM', 'description' => 'Layanan Pengawasan Obat dan Makanan'],
            ['name' => 'Klinik Rumah Swadaya', 'inisial' => 'KRS', 'description' => 'Layanan Klinik Rumah Swadaya'],
            ['name' => 'Kemenag Sawahlunto', 'inisial' => 'KMG', 'description' => 'Layanan Kementerian Agama'],
            ['name' => 'Pengadilan Negeri Sawahlunto', 'inisial' => 'PN', 'description' => 'Layanan Hukum Pengadilan Negeri'],
            ['name' => 'Disdukcapil', 'inisial' => 'DDK', 'description' => 'Dinas Kependudukan dan Pencatatan Sipil Kota Sawahlunto'],
            ['name' => 'SPKT', 'inisial' => 'SPKT', 'description' => 'Sentra Pelayanan Kepolisian Terpadu'],
            ['name' => 'SKCK', 'inisial' => 'SKCK', 'description' => 'Layanan Pembuatan SKCK Kepolisian'],
            ['name' => 'Kejaksaan Negeri Sawahlunto', 'inisial' => 'KJKS', 'description' => 'Layanan Hukum Kejaksaan Negeri'],
            ['name' => 'Kantor Imigrasi Kelas I TPI Padang', 'inisial' => 'IMI', 'description' => 'Layanan Paspor dan Keimigrasian'],
        ];

        foreach ($departments as $dept) {
            Department::updateOrCreate(
                ['inisial' => $dept['inisial']],
                [
                    'name' => $dept['name'],
                    'description' => $dept['description'],
                ]
            );
        }
    }
}
