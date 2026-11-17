<?php

namespace App\Services;

use App\Models\Eksemplar;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Membentuk data Buku Induk / Buku Inventaris secara otomatis dari koleksi
 * non paket (inventori + eksemplar + katalog), mengikuti format BUKU-INDUK.xls:
 *
 * No | TGL | No Inventaris | Pengarang | Judul | Penerbit | Jumlah (Jdl, Eks) |
 * Asal: Beli (Jdl, Eks), Hadiah (Jdl, Eks) | Klasifikasi 0-9 | Call Number |
 * Bahasa (Ind, Ingg, Lain) | Harga Rp. | Ket
 *
 * Satu baris = satu eksemplar. Kolom "Jdl" bernilai 1 pada eksemplar pertama
 * sebuah judul (inventori) sehingga total Jdl = jumlah judul.
 */
class BukuInduk
{
    public const KLASIFIKASI_DDC = [
        0 => 'Karya Umum',
        1 => 'Filsafat & Psikologi',
        2 => 'Agama',
        3 => 'Ilmu Sosial',
        4 => 'Bahasa',
        5 => 'Ilmu Murni (Sains)',
        6 => 'Ilmu Terapan (Teknologi)',
        7 => 'Kesenian & Olahraga',
        8 => 'Kesusastraan',
        9 => 'Sejarah & Geografi',
    ];

    /**
     * Query eksemplar sesuai filter, urut berdasarkan nomor induk.
     */
    public static function query(Request $request): Builder
    {
        $query = Eksemplar::query()
            ->select('eksemplar.*')
            ->join('inventori', 'inventori.id', '=', 'eksemplar.id_inventori')
            ->with(['inventori.penerbit', 'inventori.jenisSumber', 'inventori.katalog']);

        if ($tahun = $request->input('tahun')) {
            $query->whereYear('inventori.tanggal_pembelian', (int) $tahun);
        }

        if ($dari = $request->input('induk_dari')) {
            $query->whereRaw('CAST(eksemplar.no_induk AS UNSIGNED) >= ?', [(int) $dari]);
        }
        if ($sampai = $request->input('induk_sampai')) {
            $query->whereRaw('CAST(eksemplar.no_induk AS UNSIGNED) <= ?', [(int) $sampai]);
        }

        if ($asal = $request->input('asal')) {
            $query->whereHas('inventori.jenisSumber', function ($q) use ($asal) {
                $kataHadiah = ['hibah', 'hadiah', 'sumbangan', 'donasi', 'bantuan'];
                if ($asal === 'hadiah') {
                    $q->where(function ($q2) use ($kataHadiah) {
                        foreach ($kataHadiah as $k) {
                            $q2->orWhere('nama_sumber', 'like', "%{$k}%");
                        }
                    });
                } else {
                    foreach ($kataHadiah as $k) {
                        $q->where('nama_sumber', 'not like', "%{$k}%");
                    }
                }
            });
        }

        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('inventori.judul_buku', 'like', "%{$search}%")
                    ->orWhere('inventori.pengarang', 'like', "%{$search}%")
                    ->orWhere('eksemplar.no_inventori', 'like', "%{$search}%")
                    ->orWhere('eksemplar.no_induk', 'like', "%{$search}%");
            });
        }

        return $query->orderByRaw('CAST(eksemplar.no_induk AS UNSIGNED)')->orderBy('eksemplar.id');
    }

    public static function isHadiah(?string $namaSumber): bool
    {
        return (bool) preg_match('/hibah|hadiah|sumbangan|donasi|bantuan/i', (string) $namaSumber);
    }

    /** Digit kelas utama DDC (0-9) dari kode DDC / nomor panggil. */
    public static function digitKlasifikasi(?string $kodeDdc, ?string $noPanggil = null): ?int
    {
        foreach ([$kodeDdc, $noPanggil] as $kode) {
            $kode = ltrim((string) $kode);
            if ($kode !== '' && ctype_digit($kode[0])) {
                return (int) $kode[0];
            }
        }
        return null;
    }

    /**
     * Call number otomatis bila nomor panggil kosong:
     * {kode DDC} {3 huruf nama belakang pengarang} {1 huruf judul}, mis. "808.9 Liy K".
     */
    public static function callNumber($katalog, $inventori): string
    {
        if ($katalog && trim((string) $katalog->no_panggil) !== '') {
            return trim($katalog->no_panggil);
        }

        $ddc = trim((string) optional($katalog)->kode_ddc);
        if ($ddc === '') {
            return '';
        }

        $pengarang = trim(preg_replace('/[^\pL\s]/u', ' ', (string) $inventori->pengarang));
        $kata = preg_split('/\s+/', $pengarang, -1, PREG_SPLIT_NO_EMPTY);
        $belakang = $kata ? end($kata) : '';
        $kodePengarang = $belakang ? mb_convert_case(mb_substr($belakang, 0, 3), MB_CASE_TITLE) : '';

        $judul = preg_replace('/^(the|a|an)\s+/i', '', trim((string) $inventori->judul_buku));
        $kodeJudul = $judul !== '' ? mb_strtoupper(mb_substr($judul, 0, 1)) : '';

        return trim("$ddc $kodePengarang $kodeJudul");
    }

    public static function kodeBahasa(?string $bahasa): string
    {
        $b = mb_strtolower(trim((string) $bahasa));
        if ($b === '' || str_starts_with($b, 'ind')) {
            return 'ind';
        }
        if (str_starts_with($b, 'ing') || str_starts_with($b, 'eng')) {
            return 'ing';
        }
        return 'lain';
    }

    /**
     * Ubah koleksi eksemplar menjadi baris-baris buku induk.
     */
    public static function rows(Collection $eksemplar): Collection
    {
        $judulTercatat = [];

        return $eksemplar->map(function ($e) use (&$judulTercatat) {
            $inv = $e->inventori;
            $katalog = $inv ? $inv->katalog->first() : null;
            $judulBaru = $inv && !isset($judulTercatat[$inv->id]);
            if ($inv) {
                $judulTercatat[$inv->id] = true;
            }
            $hadiah = self::isHadiah(optional(optional($inv)->jenisSumber)->nama_sumber);
            $jdl = $judulBaru ? 1 : 0;

            $ket = '';
            if (in_array($e->status, ['rusak', 'hilang'])) {
                $ket = ucfirst($e->status);
            }

            return (object) [
                'no' => is_numeric($e->no_induk) ? (int) $e->no_induk : $e->no_induk,
                'no_induk' => $e->no_induk,
                'tgl' => $inv && $inv->tanggal_pembelian ? Carbon::parse($inv->tanggal_pembelian)->format('d/m/Y') : '',
                'no_inventori' => $e->no_inventori,
                'pengarang' => $inv->pengarang ?? '',
                'judul' => $inv->judul_buku ?? '',
                'penerbit' => optional(optional($inv)->penerbit)->nama_penerbit ?? '',
                'jdl' => $jdl,
                'eks' => 1,
                'beli_jdl' => $hadiah ? 0 : $jdl,
                'beli_eks' => $hadiah ? 0 : 1,
                'hadiah_jdl' => $hadiah ? $jdl : 0,
                'hadiah_eks' => $hadiah ? 1 : 0,
                'klas' => self::digitKlasifikasi(optional($katalog)->kode_ddc, optional($katalog)->no_panggil),
                'call_number' => $inv ? self::callNumber($katalog, $inv) : '',
                'bahasa' => self::kodeBahasa($inv->bahasa ?? null),
                'harga' => (float) ($inv->harga_satuan ?? 0),
                'ket' => $ket,
            ];
        });
    }

    public static function totalAwal(): array
    {
        return ['jdl' => 0, 'eks' => 0, 'beli_jdl' => 0, 'beli_eks' => 0, 'hadiah_jdl' => 0, 'hadiah_eks' => 0, 'harga' => 0];
    }

    public static function tambahTotal(array $total, $row): array
    {
        foreach (['jdl', 'eks', 'beli_jdl', 'beli_eks', 'hadiah_jdl', 'hadiah_eks', 'harga'] as $k) {
            $total[$k] += $row->$k;
        }
        return $total;
    }

    public static function formatHarga($harga): string
    {
        return $harga > 0 ? number_format($harga, 0, ',', '.') . ',-' : '-';
    }

    /**
     * Pecah baris menjadi halaman untuk PDF, sekaligus menghitung JUMLAH
     * pindahan (dari halaman sebelumnya) & JUMLAH kumulatif tiap halaman,
     * persis seperti pola buku induk manual.
     */
    public static function paginasi(Collection $rows, int $barisHalamanPertama, int $barisPerHalaman, ?array $totalAwal = null): array
    {
        $halaman = [];
        $total = $totalAwal ?? self::totalAwal();
        $sisa = $rows->values();
        $pertama = true;

        while ($sisa->isNotEmpty() || $pertama) {
            $ambil = $pertama ? $barisHalamanPertama : $barisPerHalaman;
            $isi = $sisa->take($ambil);
            $sisa = $sisa->slice($ambil)->values();

            $pindahan = $total;
            foreach ($isi as $r) {
                $total = self::tambahTotal($total, $r);
            }

            $halaman[] = [
                'rows' => $isi,
                // halaman pertama file bagian lanjutan tetap membawa JUMLAH pindahan
                'pindahan' => ($pertama && $totalAwal === null) ? null : $pindahan,
                'jumlah' => $total,
            ];
            $pertama = false;
        }

        return $halaman;
    }

    /** Jumlah eksemplar per file PDF (batas memori dompdf, ±20 halaman A4). */
    public const PDF_BARIS_PER_BAGIAN = 460;

    /**
     * Daftar bagian file PDF: [['bagian'=>1,'dari'=>no,'sampai'=>no,'jumlah'=>n], ...]
     */
    public static function daftarBagian(Collection $rows): array
    {
        $bagian = [];
        foreach ($rows->values()->chunk(self::PDF_BARIS_PER_BAGIAN) as $i => $chunk) {
            $bagian[] = [
                'bagian' => $i + 1,
                'dari' => $chunk->first()->no,
                'sampai' => $chunk->last()->no,
                'jumlah' => $chunk->count(),
            ];
        }
        return $bagian;
    }
}
