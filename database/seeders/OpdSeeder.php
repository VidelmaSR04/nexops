<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

/**
 * Impor nama OPD dari data_opd.pdf (52 OPD) ke tabel customers.
 * Aman dijalankan berulang: OPD yang namanya sudah ada tidak diduplikasi.
 * Jalankan: php artisan db:seed --class=OpdSeeder
 */
class OpdSeeder extends Seeder
{
    public function run(): void
    {
        $opds = [
        'Dinas Komunikasi dan Informatika',
        'Sekretariat Daerah',
        'Badan Perencanaan Pembangunan Riset Daerah',
        'Dinas Pemberdayaan Masyarakat dan Desa',
        'Dinas Pendidikan dan Kebudayaan',
        'Sekretariat DPRD',
        'Dinas Kesehatan',
        'Dinas Lingkungan Hidup',
        'Satuan Polisi Pamong Praja',
        'Dinas Sosial',
        'Dinas Ketenagakerjaan',
        'Dinas Kependudukan dan Pencatatan Sipil',
        'Dinas Perhubungan',
        'Dinas Penanaman Modal Pelayanan Terpadu Satu Pintu',
        'Dinas Perpustakaan dan Kearsipan',
        'Dinas Pariwisata',
        'Badan Kepegawaian dan Pengembangan Sumber Daya Manusia',
        'Badan Kesatuan Bangsa dan Politik',
        'Badan Penanggulangan Bencana Daerah',
        'Kantor Kecamatan Banyuputih',
        'Kantor Kecamatan Asembagus',
        'Kantor Kecamatan Jangkar',
        'Kantor Kecamatan Arjasa',
        'Kantor Kecamatan Kapongan',
        'Kantor Kecamatan Panji',
        'Kantor Kecamatan Mangaran',
        'Kantor Kecamatan Situbondo',
        'Kantor Kecamatan Panarukan',
        'Kantor Kecamatan Kendit',
        'Kantor Kecamatan Bungatan',
        'Kantor Kecamatan Mlandingan',
        'Kantor Kecamatan Suboh',
        'Kantor Kecamatan Jatibanteng',
        'Kantor Kecamatan Sumbermalang',
        'Kantor Kecamatan Besuki',
        'Kantor Kecamatan Banyuglugur',
        'Rumah Sakit Umum Daerah (RSUD) Dr. Abdoer Rahem',
        'Rumah Sakit Umum Daerah (RSUD) Besuki',
        'Rumah Sakit Umum Daerah (RSUD) Asembagus',
        'Kelurahan Ardirejo',
        'Kelurahan Mimbaan',
        'Kelurahan Patokan',
        'Kelurahan Dawuhan',
        'Dinas Pertanian dan Ketahanan Pangan',
        'Dinas Koperasi, Perindustrian dan Perdagangan',
        'Dinas Peternakan dan Perikanan',
        'Badan Pendapatan Daerah',
        'Badan Keuangan dan Aset Daerah',
        'Dinas Pekerjaan Umum dan Perumahan Permukiman',
        'Dinas Pemberdayaan Perempuan, Perlindungan Anak, Pengendalian Penduduk dan Keluarga Berencana',
        'Inspektorat',
        'Kantor DWP',
        ];

        $hasArea = Schema::hasColumn('customers', 'area');

        foreach ($opds as $name) {
            $values = ['type' => 'opd'];

            // Isi area otomatis untuk kantor kecamatan dan kelurahan
            if ($hasArea && preg_match('/^(Kantor Kecamatan|Kelurahan) (.+)$/', $name, $m)) {
                $values['area'] = ($m[1] === 'Kelurahan' ? 'Kel. ' : 'Kec. ') . $m[2];
            }

            $customer = Customer::firstOrNew(['name' => $name]);
            if (!$customer->exists) {
                $customer->customer_code = 'CST' . str_pad((Customer::max('id') ?? 0) + 1, 4, '0', STR_PAD_LEFT);
            }
            $customer->fill($values)->save();
        }
    }
}
