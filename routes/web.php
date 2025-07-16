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

use Illuminate\Support\Facades\Http;

Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
Route::get('/katalog/{id}', [WelcomeController::class, 'show'])->name('detail-buku');

Route::get('/debug-gemini', function () {
    $key = env('GEMINI_API_KEY');
    $model = env('GEMINI_MODEL', 'gemini-1.5-flash-latest');
    $response = Http::withHeaders([
        'X-Goog-Api-Key' => $key,
        'Content-Type' => 'application/json'
    ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", [
        'contents' => [['parts' => [['text' => 'Halo']]]]
    ]);

    return $response->json();
});


Route::get('/informasi', function () {
    return view('informasi');
})->name('informasi');

// Admin
Route::middleware(['auth', 'role:admin,pustakawan'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardAdminController::class, 'index'])->name('dashboard.index');

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

    //katalog
    Route::get('/katalog', [KatalogController::class, 'index'])->name('katalog.index');
    Route::get('/katalog/create', [KatalogController::class, 'create'])->name('katalog.create');
    Route::post('/katalog', [KatalogController::class, 'store'])->name('katalog.store');
    Route::get('/katalog/{id}/edit', [KatalogController::class, 'edit'])->name('katalog.edit');
    Route::put('/katalog/{id}', [KatalogController::class, 'update'])->name('katalog.update');
    Route::delete('/katalog/{id}', [KatalogController::class, 'destroy'])->name('katalog.destroy');
    Route::post('/generate-ringkasan', [KatalogController::class, 'generateRingkasan'])->name('katalog.generate-ringkasan');
    Route::get('/katalog/fetch-cover/{isbn}', [KatalogController::class, 'fetchCoverByIsbn'])->name('katalog.fetch-cover');


    //Katalog-Paket Buku
    Route::get('/paket-buku', [PaketBukuController::class, 'index'])->name('paket.index');
    Route::get('/paket-buku/create', [PaketBukuController::class, 'create'])->name('paket.create');
    Route::post('/paket-buku', [PaketBukuController::class, 'store'])->name('paket.store');
    Route::get('/paket-buku/{id}/edit', [PaketBukuController::class, 'edit'])->name('paket.edit');
    Route::put('/paket-buku/{id}', [PaketBukuController::class, 'update'])->name('paket.update');
    Route::delete('/paket-buku/{id}', [PaketBukuController::class, 'destroy'])->name('paket.destroy');




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
Route::get('/api/eksemplar/{no_rfid}', function ($no_rfid) {
    $eksemplar = \App\Models\Eksemplar::with('inventori')
        ->where('no_rfid', $no_rfid)
        ->first();

    if (!$eksemplar) {
        return response()->json(['error' => 'Eksemplar tidak ditemukan'], 404);
    }

    return response()->json([
        'id' => $eksemplar->id,
        'no_rfid' => $eksemplar->no_rfid,
        'judul_buku' => $eksemplar->inventori->judul_buku ?? '(judul tidak ditemukan)',
    ]);
});

// Pustakawan
// Route::middleware(['auth', 'role:pustakawan'])->prefix('pustakawan')->name('pustakawan.')->group(function () {
//     Route::get('/dashboard', [DashboardPustakawanController::class, 'index'])->name('dashboard.index');
// });

// Anggota
Route::middleware(['auth', 'role:anggota'])->prefix('anggota')->name('anggota.')->group(function () {
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

    //peminjaman-paket
    Route::get('/peminjaman-paket', [PeminjamanPaketAnggotaController::class, 'index'])->name('peminjaman-paket.index');
    Route::post('/peminjaman-paket/store', [PeminjamanPaketAnggotaController::class, 'store'])->name('peminjaman-paket.store');
});


require __DIR__ . '/auth.php';
