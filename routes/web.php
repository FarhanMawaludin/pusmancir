<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardAdminController;
use App\Http\Controllers\DashboardPustakawanController;
use App\Http\Controllers\DashboardAnggotaController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\JenisMediaController;
use App\Http\Controllers\JenisSumberController;
use App\Http\Controllers\SumberController;
use App\Http\Controllers\KategoriBukuController;
use App\Http\Controllers\PenerbitController;
use App\Http\Controllers\SekolahController;
use App\Http\Controllers\InventoriController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\EksemplarController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\PengembalianController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\ProfilAnggotaController;
use App\Http\Controllers\KatalogAnggotaController;
use App\Http\Controllers\PaketBukuController;
use App\Http\Controllers\PeminjamanAnggotaController;
use App\Http\Controllers\PeminjamanPaketController;
use App\Http\Controllers\PengembalianPaketController;
use App\Http\Controllers\KatalogPaketAnggotaController;
use App\Http\Controllers\PeminjamanPaketAnggotaController;
use App\Http\Controllers\BukuTamuController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\PeringkatController;
use App\Http\Controllers\KodeJenisSuratController;
use App\Http\Controllers\InstansiController;
use App\Http\Controllers\SuratKeluarController;
use App\Http\Controllers\SuratMasukController;
use App\Http\Controllers\BukuElektronikController;
use App\Http\Controllers\PengaduanController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\KoleksiController;
use App\Http\Controllers\PeminjamanKoleksiController;
use Illuminate\Support\Facades\Http;

Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

Route::get('/katalog/{id}', [WelcomeController::class, 'show'])->name('detail-buku');

Route::get('/ebook/{id}', [WelcomeController::class, 'showEbook'])->name('detail-buku-elektronik');

Route::get('/koleksi/{id}', [WelcomeController::class, 'showKoleksi'])->name('detail-koleksi');

Route::get('/berita', [BeritaController::class, 'indexPublish'])->name('berita.index');
Route::get('/berita/{id}', [BeritaController::class, 'show'])->name('berita.show');

Route::get('/peringkat', [PeringkatController::class, 'index'])->name('peringkat.index');

// Buku Tamu - Bisa diakses publik tanpa login
Route::get('/buku-tamu', [BukuTamuController::class, 'create'])->name('form');
Route::post('/buku-tamu', [BukuTamuController::class, 'store'])->name('store');


Route::get('/pengaduan', [PengaduanController::class, 'create'])->name('pengaduan');
Route::post('/pengaduan', [PengaduanController::class, 'store'])->name('pengaduan.store');



Route::get('/informasi', function () {
    return view('informasi');
})->name('informasi');

Route::post('/track-visit', [DashboardAdminController::class, 'trackVisit'])->name('track.visit');

// Admin
Route::middleware(['auth', 'role:admin,pustakawan', 'prevent.back'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardAdminController::class, 'index'])->name('dashboard.index');

    //backup
    Route::get('/backup', [BackupController::class, 'index'])->name('backup.index');
    Route::get('/backup/run', [BackupController::class, 'backupDatabase'])->name('backup.run');
    Route::post('admin/backup/import', [BackupController::class, 'importDatabase'])->name('backup.import');


    //Kelola Kelas
    Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
    Route::get('/kelas/create', [KelasController::class, 'create'])->name('kelas.create');
    Route::post('/kelas', [KelasController::class, 'store'])->name('kelas.store');
    Route::get('/kelas/{id}', [KelasController::class, 'show'])->name('kelas.show');
    Route::get('/kelas/{id}/edit', [KelasController::class, 'edit'])->name('kelas.edit');
    Route::put('/kelas/{id}', [KelasController::class, 'update'])->name('kelas.update');
    Route::delete('/kelas/{id}', [KelasController::class, 'destroy'])->name('kelas.destroy');

    //Kelola Pengguna
    Route::get('/pengguna', [PenggunaController::class, 'index'])->name('pengguna.index');
    Route::get('/pengguna/create', [PenggunaController::class, 'create'])->name('pengguna.create');
    Route::post('/pengguna', [PenggunaController::class, 'store'])->name('pengguna.store');
    Route::get('/pengguna/{id}', [PenggunaController::class, 'show'])->name('pengguna.show');
    Route::get('/pengguna/{id}/edit', [PenggunaController::class, 'edit'])->name('pengguna.edit');
    Route::put('/pengguna/{id}', [PenggunaController::class, 'update'])->name('pengguna.update');
    Route::delete('/pengguna/{id}', [PenggunaController::class, 'destroy'])->name('pengguna.destroy');
    Route::post('/import', [PenggunaController::class, 'import'])->name('pengguna.import');

    //Daftar Anggota
    Route::get('/anggota', [AnggotaController::class, 'index'])->name('anggota.index');
    Route::get('/anggota/alumni', [AnggotaController::class, 'indexAlumni'])->name('anggota.indexAlumni');
    Route::post('/anggota/set-alumni', [AnggotaController::class, 'setAlumni'])->name('anggota.setAlumni');
    Route::post('/anggota/set-aktif', [AnggotaController::class, 'setAktif'])->name('anggota.setAktif');
    Route::get('/anggota/{id}', [AnggotaController::class, 'show'])->name('anggota.show');


    //Data Master Inventori
    //Jenis Media
    Route::get('/jenis-media', [JenisMediaController::class, 'index'])->name('jenis-media.index');
    Route::get('/jenis-media/create', [JenisMediaController::class, 'create'])->name('jenis-media.create');
    Route::post('/jenis-media', [JenisMediaController::class, 'store'])->name('jenis-media.store');
    Route::get('/jenis-media/{id}/edit', [JenisMediaController::class, 'edit'])->name('jenis-media.edit');
    Route::put('/jenis-media/{id}', [JenisMediaController::class, 'update'])->name('jenis-media.update');
    Route::delete('/jenis-media/{id}', [JenisMediaController::class, 'destroy'])->name('jenis-media.destroy');

    //Jenis Sumber
    Route::get('/jenis-sumber', [JenisSumberController::class, 'index'])->name('jenis-sumber.index');
    Route::get('/jenis-sumber/create', [JenisSumberController::class, 'create'])->name('jenis-sumber.create');
    Route::post('/jenis-sumber', [JenisSumberController::class, 'store'])->name('jenis-sumber.store');
    Route::get('/jenis-sumber/{id}/edit', [JenisSumberController::class, 'edit'])->name('jenis-sumber.edit');
    Route::put('/jenis-sumber/{id}', [JenisSumberController::class, 'update'])->name('jenis-sumber.update');
    Route::delete('/jenis-sumber/{id}', [JenisSumberController::class, 'destroy'])->name('jenis-sumber.destroy');

    //Sumber
    Route::get('/sumber', [SumberController::class, 'index'])->name('sumber.index');
    Route::get('/sumber/create', [SumberController::class, 'create'])->name('sumber.create');
    Route::post('/sumber', [SumberController::class, 'store'])->name('sumber.store');
    Route::get('/sumber/{id}/edit', [SumberController::class, 'edit'])->name('sumber.edit');
    Route::put('/sumber/{id}', [SumberController::class, 'update'])->name('sumber.update');
    Route::delete('/sumber/{id}', [SumberController::class, 'destroy'])->name('sumber.destroy');

    //Kategori Buku
    Route::get('/kategori-buku', [KategoriBukuController::class, 'index'])->name('kategori-buku.index');
    Route::get('/kategori-buku/create', [KategoriBukuController::class, 'create'])->name('kategori-buku.create');
    Route::post('/kategori-buku', [KategoriBukuController::class, 'store'])->name('kategori-buku.store');
    Route::get('/kategori-buku/{id}/edit', [KategoriBukuController::class, 'edit'])->name('kategori-buku.edit');
    Route::put('/kategori-buku/{id}', [KategoriBukuController::class, 'update'])->name('kategori-buku.update');
    Route::delete('/kategori-buku/{id}', [KategoriBukuController::class, 'destroy'])->name('kategori-buku.destroy');

    //Penerbit
    Route::get('/penerbit', [PenerbitController::class, 'index'])->name('penerbit.index');
    Route::get('/penerbit/create', [PenerbitController::class, 'create'])->name('penerbit.create');
    Route::post('/penerbit', [PenerbitController::class, 'store'])->name('penerbit.store');
    Route::get('/penerbit/{id}/edit', [PenerbitController::class, 'edit'])->name('penerbit.edit');
    Route::put('/penerbit/{id}', [PenerbitController::class, 'update'])->name('penerbit.update');
    Route::delete('/penerbit/{id}', [PenerbitController::class, 'destroy'])->name('penerbit.destroy');

    //Sekolah
    Route::get('/sekolah', [SekolahController::class, 'index'])->name('sekolah.index');
    Route::get('/sekolah/create', [SekolahController::class, 'create'])->name('sekolah.create');
    Route::post('/sekolah', [SekolahController::class, 'store'])->name('sekolah.store');
    Route::get('/sekolah/{id}/edit', [SekolahController::class, 'edit'])->name('sekolah.edit');
    Route::put('/sekolah/{id}', [SekolahController::class, 'update'])->name('sekolah.update');
    Route::delete('/sekolah/{id}', [SekolahController::class, 'destroy'])->name('sekolah.destroy');

    //inventori
    Route::get('/inventori', [InventoriController::class, 'index'])->name('inventori.index');
    Route::get('/inventori/create', [InventoriController::class, 'create'])->name('inventori.create');
    Route::post('/inventori', [InventoriController::class, 'store'])->name('inventori.store');
    Route::get('/inventori/{id}', [InventoriController::class, 'show'])->name('inventori.show');
    Route::get('/inventori/{id}/edit', [InventoriController::class, 'edit'])->name('inventori.edit');
    Route::put('/inventori/{id}', [InventoriController::class, 'update'])->name('inventori.update');
    Route::delete('/inventori/{id}', [InventoriController::class, 'destroy'])->name('inventori.destroy');
    Route::get('/admin/inventori/export', [InventoriController::class, 'export'])->name('inventori.export');
    Route::post('/admin/inventori/import', [InventoriController::class, 'import'])->name('inventori.import');

    //inventori Koleksi
    Route::get('/koleksi', [KoleksiController::class, 'index'])->name('koleksi.index');
    Route::get('/koleksi/create', [KoleksiController::class, 'create'])->name('koleksi.create');
    Route::post('/koleksi', [KoleksiController::class, 'store'])->name('koleksi.store');
    Route::get('/koleksi/{id}', [KoleksiController::class, 'show'])->name('koleksi.show');
    Route::get('/koleksi/{id}/edit', [KoleksiController::class, 'edit'])->name('koleksi.edit');
    Route::put('/koleksi/{id}', [KoleksiController::class, 'update'])->name('koleksi.update');
    Route::delete('/koleksi/{id}', [KoleksiController::class, 'destroy'])->name('koleksi.destroy');
    Route::put('/admin/koleksi/{id}/ubah-status', [KoleksiController::class, 'ubahStatus'])->name('koleksi.ubahStatus');
    Route::get('/admin/koleksi/export', [KoleksiController::class, 'exportExcel'])->name('koleksi.export');




    //katalog
    Route::get('/katalog', [KatalogController::class, 'index'])->name('katalog.index');
    Route::get('/katalog/create', [KatalogController::class, 'create'])->name('katalog.create');
    Route::post('/katalog', [KatalogController::class, 'store'])->name('katalog.store');
    Route::get('/katalog/{id}/edit', [KatalogController::class, 'edit'])->name('katalog.edit');
    Route::put('/katalog/{id}', [KatalogController::class, 'update'])->name('katalog.update');
    Route::delete('/katalog/{id}', [KatalogController::class, 'destroy'])->name('katalog.destroy');
    Route::post('/generate-ringkasan', [KatalogController::class, 'generateRingkasan'])->name('katalog.generate-ringkasan');
    Route::post('/generate-ddc', [KatalogController::class, 'generateKodeDDC'])->name('katalog.generate-ddc');
    Route::post('/generate-isbn', [KatalogController::class, 'generateISBN'])->name('katalog.generate-isbn');
    Route::get('/katalog/fetch-cover/{isbn}', [KatalogController::class, 'fetchCoverByIsbn'])->name('katalog.fetch-cover');


    //Katalog-Paket Buku
    Route::get('/paket-buku', [PaketBukuController::class, 'index'])->name('paket.index');
    Route::get('/paket-buku/create', [PaketBukuController::class, 'create'])->name('paket.create');
    Route::post('/paket-buku', [PaketBukuController::class, 'store'])->name('paket.store');
    Route::get('/paket-buku/{id}/edit', [PaketBukuController::class, 'edit'])->name('paket.edit');
    Route::put('/paket-buku/{id}', [PaketBukuController::class, 'update'])->name('paket.update');
    Route::delete('/paket-buku/{id}', [PaketBukuController::class, 'destroy'])->name('paket.destroy');

    //Katalog-Buku Elektronik
    Route::get('/buku-elektronik', [BukuElektronikController::class, 'index'])->name('buku-elektronik.index');
    Route::get('/buku-elektronik/create', [BukuElektronikController::class, 'create'])->name('buku-elektronik.create');
    Route::post('/buku-elektronik', [BukuElektronikController::class, 'store'])->name('buku-elektronik.store');
    Route::get('/buku-elektronik/{id}/edit', [BukuElektronikController::class, 'edit'])->name('buku-elektronik.edit');
    Route::put('/buku-elektronik/{id}', [BukuElektronikController::class, 'update'])->name('buku-elektronik.update');
    Route::delete('/buku-elektronik/{id}', [BukuElektronikController::class, 'destroy'])->name('buku-elektronik.destroy');


    //Eksemplar
    Route::get('/eksemplar', [EksemplarController::class, 'index'])->name('eksemplar.index');
    Route::get('/eksemplar/{id}/edit', [EksemplarController::class, 'edit'])->name('eksemplar.edit');
    Route::put('/eksemplar/{id}', [EksemplarController::class, 'update'])->name('eksemplar.update');
    Route::get('/admin/eksemplar/{id}/cetak-barcode', [EksemplarController::class, 'cetakBarcode'])
        ->name('eksemplar.cetakBarcode');
    Route::post('/eksemplar/cetak-batch', [EksemplarController::class, 'cetakBatch'])->name('eksemplar.cetak-batch');
    //Eksemplar Status Buku
    Route::get('/eksemplar/buku', [EksemplarController::class, 'indexBuku'])->name('eksemplar.buku');


    //Peminjaman Non Paket
    Route::get('/peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
    Route::post('/peminjaman/store', [PeminjamanController::class, 'store'])->name('peminjaman.store');
    Route::patch('/peminjaman/{id}/status', [PeminjamanController::class, 'updateStatus'])
        ->name('peminjaman.updateStatus');

    //Pengembalian Non Paket
    Route::get('/pengembalian', [PengembalianController::class, 'index'])->name('pengembalian.index');
    Route::put('/pengembalian/update/{id}', [PengembalianController::class, 'update'])->name('pengembalian.update');
    Route::get('/pengembalian/{id}/export-surat-terlambat', [PengembalianController::class, 'exportSuratTerlambat'])
        ->name('pengembalian.export-surat-terlambat');
    Route::post('/pengembalian/{id}/kirim-wa', [PengembalianController::class, 'kirimWhatsapp'])
        ->name('pengembalian.kirim_wa');


    //Peminjaman Paket
    Route::get('/peminjaman-paket', [PeminjamanPaketController::class, 'index'])->name('peminjaman-paket.index');
    Route::patch('/peminjaman-paket/{id}/status', [PeminjamanPaketController::class, 'updateStatus'])
        ->name('peminjaman-paket.updateStatus');


    //Pengembalian Paket
    Route::get('/pengembalian-paket', [PengembalianPaketController::class, 'index'])->name('pengembalian-paket.index');
    Route::put('/pengembalian-paket/update/{id}', [PengembalianPaketController::class, 'update'])->name('pengembalian-paket.update');

    //Peminjaman Koleksi
    Route::get('/peminjaman-koleksi', [PeminjamanKoleksiController::class, 'index'])->name('peminjaman-koleksi.index');
    Route::post('/peminjaman-koleksi/store', [PeminjamanKoleksiController::class, 'store'])->name('peminjaman-koleksi.store');
    Route::patch('/peminjaman-koleksi/{id}/status', [PeminjamanKoleksiController::class, 'updateStatus'])
        ->name('peminjaman-koleksi.updateStatus');
    Route::patch('admin/peminjaman-koleksi/{id}/kembalikan', [PeminjamanKoleksiController::class, 'kembalikan'])
        ->name('peminjaman-koleksi.kembalikan');
    Route::get('/admin/peminjaman-koleksi/export', [PeminjamanKoleksiController::class, 'export'])->name('peminjaman-koleksi.export');


    //Buku Tamu
    // Route::get('/buku-tamu', [BukuTamuController::class, 'create'])->name('buku-tamu.form');
    // Route::post('/buku-tamu', [BukuTamuController::class, 'store'])->name('buku-tamu.store');
    Route::get('/buku-tamu/log-tamu', [BukuTamuController::class, 'LogTamu'])->name('buku-tamu.log-tamu');
    Route::get('/log-tamu/export', [BukuTamuController::class, 'exportLogTamuExcel'])->name('buku-tamu.export');



    //Kode Jenis Surat
    Route::get('/kode-jenis-surat', [KodeJenisSuratController::class, 'index'])->name('kode-jenis-surat.index');
    Route::get('/kode-jenis-surat/create', [KodeJenisSuratController::class, 'create'])->name('kode-jenis-surat.create');
    Route::post('/kode-jenis-surat', [KodeJenisSuratController::class, 'store'])->name('kode-jenis-surat.store');
    Route::get('/kode-jenis-surat/{id}/edit', [KodeJenisSuratController::class, 'edit'])->name('kode-jenis-surat.edit');
    Route::put('/kode-jenis-surat/{id}', [KodeJenisSuratController::class, 'update'])->name('kode-jenis-surat.update');
    Route::delete('/kode-jenis-surat/{id}', [KodeJenisSuratController::class, 'destroy'])->name('kode-jenis-surat.destroy');

    //Instansi
    Route::get('/instansi', [InstansiController::class, 'index'])->name('instansi.index');
    Route::get('/instansi/create', [InstansiController::class, 'create'])->name('instansi.create');
    Route::post('/instansi', [InstansiController::class, 'store'])->name('instansi.store');
    Route::get('/instansi/{id}/edit', [InstansiController::class, 'edit'])->name('instansi.edit');
    Route::put('/instansi/{id}', [InstansiController::class, 'update'])->name('instansi.update');
    Route::delete('/instansi/{id}', [InstansiController::class, 'destroy'])->name('instansi.destroy');

    //surat keluar
    Route::get('/surat-keluar', [SuratKeluarController::class, 'index'])->name('surat-keluar.index');
    Route::get('/surat-keluar/create', [SuratKeluarController::class, 'create'])->name('surat-keluar.create');
    Route::post('/surat-keluar', [SuratKeluarController::class, 'store'])->name('surat-keluar.store');
    Route::get('/surat-keluar/{id}/edit', [SuratKeluarController::class, 'edit'])->name('surat-keluar.edit');
    Route::put('/surat-keluar/{id}', [SuratKeluarController::class, 'update'])->name('surat-keluar.update');
    Route::delete('/surat-keluar/{id}', [SuratKeluarController::class, 'destroy'])->name('surat-keluar.destroy');
    Route::get('/surat-keluar/{id}', [SuratKeluarController::class, 'show'])->name('surat-keluar.show');

    //Surat Masuk
    Route::get('/surat-masuk', [SuratMasukController::class, 'index'])->name('surat-masuk.index');
    Route::get('/surat-masuk/create', [SuratMasukController::class, 'create'])->name('surat-masuk.create');
    Route::post('/surat-masuk', [SuratMasukController::class, 'store'])->name('surat-masuk.store');
    Route::get('/surat-masuk/{id}/edit', [SuratMasukController::class, 'edit'])->name('surat-masuk.edit');
    Route::put('/surat-masuk/{id}', [SuratMasukController::class, 'update'])->name('surat-masuk.update');
    Route::delete('/surat-masuk/{id}', [SuratMasukController::class, 'destroy'])->name('surat-masuk.destroy');
    Route::get('/surat-masuk/{id}', [SuratMasukController::class, 'show'])->name('surat-masuk.show');

    //Berita
    Route::get('/berita', [BeritaController::class, 'index'])->name('berita.index');
    Route::get('/berita/create', [BeritaController::class, 'create'])->name('berita.create');
    Route::post('/berita', [BeritaController::class, 'store'])->name('berita.store');
    Route::get('/berita/{id}/edit', [BeritaController::class, 'edit'])->name('berita.edit');
    Route::put('/berita/{id}', [BeritaController::class, 'update'])->name('berita.update');
    Route::delete('/berita/{id}', [BeritaController::class, 'destroy'])->name('berita.destroy');
    Route::get('/berita/{id}', [BeritaController::class, 'show'])->name('berita.show');


    //Data Laporan Peminjaman
    Route::get('/laporan-peminjaman/paket', [PeminjamanPaketController::class, 'laporanPeminjamanPaket'])->name('laporan.paket');
    Route::get('/admin/laporan-peminjaman/paket/export', [PeminjamanPaketController::class, 'exportLaporanPaketExcel'])->name('laporan.exportPaket');
    Route::get('/laporan-peminjaman/non-paket', [PeminjamanController::class, 'laporanPeminjamanNonPaket'])->name('laporan.non-paket');
    Route::get('/admin/laporan-peminjaman/non-paket/export', [PeminjamanController::class, 'exportLaporanNonPaketExcel'])->name('laporan.exportNonPaket');

    //PENGADUAN
    Route::get('/pengaduan-laporan', [PengaduanController::class, 'index'])->name('pengaduan.index');
    Route::get('/pengaduan-laporan/{id}', [PengaduanController::class, 'show'])->name('pengaduan.show');
    Route::patch('/admin/pengaduan/{id}/baca', [PengaduanController::class, 'markAsRead'])->name('pengaduan.baca');
});

// Cek anggota berdasarkan NISN
Route::get('/api/anggota/{nisn}', function ($nisn) {
    $anggota =  \App\Models\Anggota::with('user')->where('nisn', $nisn)->first();

    if (!$anggota) {
        return response()->json(['error' => 'Anggota tidak ditemukan'], 404);
    }

    return response()->json([
        'id' => $anggota->id,
        'nisn' => $anggota->nisn,
        'nama' => $anggota->user->name ?? '(nama tidak ditemukan)', // ambil dari relasi
    ]);
});

// Cek eksemplar berdasarkan no_rfid
// Route::get('/api/eksemplar/{no_rfid}', function ($no_rfid) {
//     $eksemplar = \App\Models\Eksemplar::with('inventori')
//         ->where('no_rfid', $no_rfid)
//         ->first();

//     if (!$eksemplar) {
//         return response()->json(['error' => 'Eksemplar tidak ditemukan'], 404);
//     }

//     return response()->json([
//         'id' => $eksemplar->id,
//         'no_rfid' => $eksemplar->no_rfid,
//         'judul_buku' => $eksemplar->inventori->judul_buku ?? '(judul tidak ditemukan)',
//     ]);
// });

Route::get('/api/eksemplar/{no_rfid?}/{no_induk?}', function ($no_rfid = null, $no_induk = null) {
    $query = \App\Models\Eksemplar::with('inventori');

    // kalau no_rfid dikirim "null", anggap tidak ada
    if ($no_rfid && $no_rfid !== 'null') {
        $query->where('no_rfid', $no_rfid);
    }

    if ($no_induk && $no_induk !== 'null') {
        $query->where('no_induk', $no_induk);
    }

    $eksemplar = $query->first();

    if (!$eksemplar) {
        return response()->json(['error' => 'Eksemplar tidak ditemukan'], 404);
    }

    return response()->json([
        'id' => $eksemplar->id,
        'no_rfid' => $eksemplar->no_rfid,
        'no_induk' => $eksemplar->no_induk,
        'judul_buku' => $eksemplar->inventori->judul_buku ?? '(judul tidak ditemukan)',
    ]);
});



Route::get('/api/koleksi/{kode}', function ($kode) {
    $koleksi = \App\Models\Koleksi::where('no_rfid', $kode)->first();

    if (!$koleksi) {
        return response()->json(['error' => 'Koleksi tidak ditemukan'], 404);
    }

    return response()->json([
        'id' => $koleksi->id,
        'judul_buku' => $koleksi->nama ?? '(judul tidak ditemukan)',
    ]);
});



// Pustakawan
// Route::middleware(['auth', 'role:pustakawan'])->prefix('pustakawan')->name('pustakawan.')->group(function () {
//     Route::get('/dashboard', [DashboardPustakawanController::class, 'index'])->name('dashboard.index');
// });

// Anggota
Route::middleware(['auth', 'role:anggota', 'prevent.back'])->prefix('anggota')->name('anggota.')->group(function () {
    Route::get('/dashboard', [DashboardAnggotaController::class, 'index'])->name('dashboard.index');

    //Profil
    Route::get('/profil', [ProfilAnggotaController::class, 'index'])->name('profil.index');
    Route::get('/profil/{id}/edit', [ProfilAnggotaController::class, 'edit'])->name('profil.edit');
    Route::put('/profil/{id}', [ProfilAnggotaController::class, 'update'])->name('profil.update');
    Route::get('/anggota/kartu', [ProfilAnggotaController::class, 'showKartu'])->name('profil.kartu');


    //Katalog
    Route::get('/katalog', [KatalogAnggotaController::class, 'index'])->name('katalog.index');
    Route::get('/katalog/{id}', [KatalogAnggotaController::class, 'show'])->name('katalog.show');

    //katalog-paket
    Route::get('/katalog-paket', [KatalogPaketAnggotaController::class, 'index'])->name('katalog-paket.index');
    Route::get('/katalog-paket/{id}', [KatalogPaketAnggotaController::class, 'show'])->name('katalog-paket.show');

    //Peminjaman
    Route::get('/peminjaman', [PeminjamanAnggotaController::class, 'index'])->name('peminjaman.index');
    Route::post('/peminjaman/store', [PeminjamanAnggotaController::class, 'store'])->name('peminjaman.store');
    Route::post('/anggota/peminjaman/{id}/perpanjang', [PeminjamanAnggotaController::class, 'perpanjang'])
        ->name('peminjaman.perpanjang');
    Route::post('/anggota/peminjaman/{id}/batal', [PeminjamanAnggotaController::class, 'batal'])->name('peminjaman.batal');


    //peminjaman-paket
    Route::get('/peminjaman-paket', [PeminjamanPaketAnggotaController::class, 'index'])->name('peminjaman-paket.index');
    Route::post('/peminjaman-paket/store', [PeminjamanPaketAnggotaController::class, 'store'])->name('peminjaman-paket.store');
    Route::post('/anggota/peminjaman-paket/{id}/batal', [PeminjamanPaketAnggotaController::class, 'batal'])->name('peminjaman-paket.batal');
});


require __DIR__ . '/auth.php';
