<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Berita;

class BeritaSeeder extends Seeder
{
    public function run()
    {
        $beritaList = [
            [
                'judul' => 'Pustaka Digital Resmi Diluncurkan di SMAN 1 Harapan Bangsa',
                'isi' => 'SMAN 1 Harapan Bangsa kini memiliki pustaka digital yang bisa diakses oleh seluruh siswa melalui website resmi perpustakaan.',
            ],
            [
                'judul' => 'Perpustakaan Gelar Workshop Literasi Digital',
                'isi' => 'Perpustakaan sekolah mengadakan workshop tentang literasi digital guna meningkatkan kemampuan siswa dalam memanfaatkan teknologi informasi.',
            ],
            [
                'judul' => 'Lomba Review Buku di Perpustakaan Sukses Digelar',
                'isi' => 'Sebanyak 50 siswa mengikuti lomba review buku yang diselenggarakan oleh Perpustakaan SMAN 1 Harapan Bangsa.',
            ],
            [
                'judul' => 'Donasi Buku: Perpustakaan Terima 200 Buku Baru dari Alumni',
                'isi' => 'Alumni SMAN 1 Harapan Bangsa mendonasikan lebih dari 200 buku untuk menambah koleksi perpustakaan.',
            ],
            [
                'judul' => 'Kegiatan Membaca Bersama Setiap Hari Jumat',
                'isi' => 'Program membaca bersama setiap Jumat pagi di perpustakaan bertujuan meningkatkan minat baca siswa.',
            ],
            [
                'judul' => 'Peningkatan Koleksi Buku Fiksi dan Non-Fiksi Tahun Ini',
                'isi' => 'Perpustakaan menambah 500 buku baru yang terdiri dari buku fiksi, non-fiksi, dan referensi akademik.',
            ],
            [
                'judul' => 'Perpustakaan Sekolah Hadirkan Layanan Peminjaman Online',
                'isi' => 'Kini siswa bisa meminjam buku secara online melalui aplikasi perpustakaan berbasis web.',
            ],
            [
                'judul' => 'Hari Literasi Nasional Diperingati di Perpustakaan Sekolah',
                'isi' => 'Perpustakaan mengadakan acara spesial dalam rangka Hari Literasi Nasional dengan menghadirkan pembicara dari Dinas Perpustakaan.',
            ],
            [
                'judul' => 'Pelatihan Manajemen Perpustakaan untuk Pengurus OSIS',
                'isi' => 'Pengurus OSIS mengikuti pelatihan pengelolaan perpustakaan sebagai bagian dari program kerja sama literasi.',
            ],
            [
                'judul' => 'Pameran Buku dan Arsip Sekolah Dibuka untuk Umum',
                'isi' => 'Perpustakaan mengadakan pameran buku dan arsip sejarah sekolah selama seminggu penuh.',
            ],
        ];

        foreach ($beritaList as $item) {
            Berita::create([
                'judul' => $item['judul'],
                'isi' => $item['isi'],
                'thumbnail' => null, // Jika punya gambar bisa diisi pathnya
                'status' => 'publish',
                'penulis' => 'Admin Pusmancir'
            ]);
        }
    }
}
