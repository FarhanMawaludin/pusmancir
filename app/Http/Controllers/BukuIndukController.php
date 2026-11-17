<?php

namespace App\Http\Controllers;

use App\Models\Eksemplar;
use App\Models\Inventori;
use App\Models\JenisMedia;
use App\Models\JenisSumber;
use App\Models\Katalog;
use App\Models\KategoriBuku;
use App\Models\Penerbit;
use App\Models\Sekolah;
use App\Models\Sumber;
use App\Services\BukuInduk;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BukuIndukController extends Controller
{
    /** Kolom klasifikasi DDC 0-9 pada format BUKU-INDUK (M..V). */
    private const KOLOM_KLAS = ['M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V'];

    public function index(Request $request)
    {
        $activeMenu = 'laporanPaket';

        $paginator = BukuInduk::query($request)->paginate(25)->withQueryString();

        // Nilai Jdl dihitung berdasarkan seluruh data terfilter (bukan per halaman web),
        // jadi ambil dulu id inventori yang sudah muncul sebelum halaman ini.
        $offset = ($paginator->currentPage() - 1) * $paginator->perPage();
        $sebelumnya = $offset > 0
            ? BukuInduk::query($request)->limit($offset)->pluck('eksemplar.id_inventori')->unique()->flip()
            : collect();

        $rows = BukuInduk::rows($paginator->getCollection())->map(function ($r, $i) use ($paginator, $sebelumnya) {
            $inventoriId = $paginator->getCollection()[$i]->id_inventori;
            if ($r->jdl && $sebelumnya->has($inventoriId)) {
                $r->jdl = $r->beli_jdl = $r->hadiah_jdl = 0;
            }
            return $r;
        });

        // Ringkasan judul, eksemplar, asal & nilai koleksi + pembagian file PDF
        $semua = BukuInduk::rows(BukuInduk::query($request)->get());
        $ringkasan = BukuInduk::totalAwal();
        foreach ($semua as $r) {
            $ringkasan = BukuInduk::tambahTotal($ringkasan, $r);
        }
        $daftarBagian = BukuInduk::daftarBagian($semua);

        $daftarTahun = Inventori::query()->pluck('tanggal_pembelian')
            ->map(fn($t) => (int) substr((string) $t, 0, 4))
            ->filter()->unique()->sortDesc()->values();

        return view('admin.laporan.buku-induk', compact('activeMenu', 'paginator', 'rows', 'ringkasan', 'daftarTahun', 'daftarBagian'));
    }

    private function judulFilter(Request $request): string
    {
        $bagian = [];
        if ($request->filled('tahun')) {
            $bagian[] = 'Tahun Pengadaan ' . $request->input('tahun');
        }
        if ($request->filled('induk_dari') || $request->filled('induk_sampai')) {
            $bagian[] = 'No. Induk ' . ($request->input('induk_dari') ?: 'awal') . ' s.d. ' . ($request->input('induk_sampai') ?: 'akhir');
        }
        if ($request->filled('asal')) {
            $bagian[] = 'Asal: ' . ($request->input('asal') === 'hadiah' ? 'Hadiah/Hibah' : 'Pembelian');
        }
        if ($request->filled('search')) {
            $bagian[] = 'Pencarian: "' . $request->input('search') . '"';
        }
        return $bagian ? implode(' | ', $bagian) : 'Seluruh Koleksi Non Paket';
    }

    public function exportPdf(Request $request)
    {
        @set_time_limit(300);
        @ini_set('memory_limit', '1024M');

        $semua = BukuInduk::rows(BukuInduk::query($request)->get())->values();
        $daftarBagian = BukuInduk::daftarBagian($semua);
        $jumlahBagian = max(1, count($daftarBagian));
        $bagian = min(max(1, (int) $request->input('bagian', 1)), $jumlahBagian);

        $perBagian = BukuInduk::PDF_BARIS_PER_BAGIAN;
        $offset = ($bagian - 1) * $perBagian;

        // JUMLAH pindahan dari bagian-bagian sebelumnya
        $totalAwal = null;
        if ($bagian > 1) {
            $totalAwal = BukuInduk::totalAwal();
            foreach ($semua->slice(0, $offset) as $r) {
                $totalAwal = BukuInduk::tambahTotal($totalAwal, $r);
            }
        }

        // 17 baris di halaman berkop, 23 baris di halaman berikutnya (A4 landscape)
        $rows = $semua->slice($offset, $perBagian)->values();
        $halaman = BukuInduk::paginasi($rows, $bagian === 1 ? 17 : 23, 23, $totalAwal);

        $keterangan = $this->judulFilter($request);
        if ($jumlahBagian > 1) {
            $info = $daftarBagian[$bagian - 1];
            $keterangan .= " | Bagian {$bagian} dari {$jumlahBagian} (No. {$info['dari']}–{$info['sampai']})";
        }

        $pdf = Pdf::loadView('admin.laporan.pdf.buku-induk', [
            'halaman' => $halaman,
            'keterangan' => $keterangan,
            'totalRows' => $semua->count(),
            'awalLanjutan' => $bagian > 1,
            'tanpaTtd' => $bagian < $jumlahBagian,
        ])->setPaper('a4', 'landscape');

        $nama = 'buku-induk-' . ($jumlahBagian > 1 ? "bagian-{$bagian}-" : '') . now()->format('Ymd-His') . '.pdf';

        return $request->boolean('download') ? $pdf->download($nama) : $pdf->stream($nama);
    }

    /**
     * Export Excel dengan tata letak sama seperti BUKU-INDUK.xls (dapat diimport kembali).
     */
    public function exportExcel(Request $request)
    {
        @set_time_limit(300);
        $rows = BukuInduk::rows(BukuInduk::query($request)->get());
        return $this->unduhSpreadsheet($rows, 'buku-induk-' . now()->format('Ymd-His') . '.xlsx');
    }

    public function template()
    {
        $contoh = collect([(object) [
            'no' => 1, 'tgl' => now()->format('d/m/Y'), 'no_inventori' => '000001/CRS/B/' . now()->year,
            'pengarang' => 'Tere Liye', 'judul' => 'Bumi', 'penerbit' => 'Gramedia Pustaka Utama',
            'jdl' => 1, 'eks' => 1, 'beli_jdl' => 1, 'beli_eks' => 1, 'hadiah_jdl' => 0, 'hadiah_eks' => 0,
            'klas' => 8, 'call_number' => '899.221 3 Lie B', 'bahasa' => 'ind', 'harga' => 103000, 'ket' => '',
        ]]);

        return $this->unduhSpreadsheet($contoh, 'template-import-buku-induk.xlsx');
    }

    private function unduhSpreadsheet($rows, string $namaFile)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('inventaris');

        $lebar = ['A' => 6, 'B' => 11, 'C' => 17, 'D' => 22, 'E' => 30, 'F' => 20, 'G' => 5, 'H' => 5, 'I' => 5, 'J' => 5,
            'K' => 5, 'L' => 5, 'W' => 14, 'X' => 4.5, 'Y' => 4.5, 'Z' => 4.5, 'AA' => 12, 'AB' => 12];
        foreach (self::KOLOM_KLAS as $c) {
            $lebar[$c] = 3.2;
        }
        foreach ($lebar as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        // Kop
        $kop = [
            'PEMERINTAH PROVINSI BANTEN',
            'DINAS PENDIDIKAN DAN KEBUDAYAAN',
            'UPT SMA NEGERI 1 CIRUAS',
            'Jalan Raya Jakarta Km 9,5 Serang Telp. 280043',
            'Situs web: www.smancir1.sch.id, Surel: ciruas@smancir1.sch.id',
        ];
        foreach ($kop as $i => $teks) {
            $r = $i + 1;
            $sheet->mergeCells("A{$r}:AB{$r}");
            $sheet->setCellValue("A{$r}", $teks);
        }
        $sheet->getStyle('A1:AB5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:A3')->getFont()->setBold(true);
        $sheet->getStyle('A3')->getFont()->setSize(14);
        $sheet->getStyle('A5:AB5')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_DOUBLE);

        $sheet->setCellValue('B6', 'Lampiran');
        $sheet->mergeCells('A8:AB8');
        $sheet->setCellValue('A8', 'BUKU INVENTARIS/INDUK');
        $sheet->getStyle('A8')->getFont()->setBold(true)->setSize(13);
        $sheet->getStyle('A8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header tabel (baris 10-12) — sama dengan BUKU-INDUK.xls
        $h = 10;
        foreach (['A' => 'No.', 'B' => 'TGL', 'C' => 'No Inventaris', 'D' => 'Pengarang', 'E' => 'Judul', 'F' => 'Penerbit',
            'W' => 'Call Number', 'AA' => 'Harga Rp.', 'AB' => 'Ket'] as $col => $teks) {
            $sheet->mergeCells("{$col}{$h}:{$col}" . ($h + 2));
            $sheet->setCellValue("{$col}{$h}", $teks);
        }
        $sheet->mergeCells("G{$h}:H{$h}");
        $sheet->setCellValue("G{$h}", 'Jumlah');
        $sheet->mergeCells("G" . ($h + 1) . ":G" . ($h + 2));
        $sheet->setCellValue("G" . ($h + 1), 'Jdl');
        $sheet->mergeCells("H" . ($h + 1) . ":H" . ($h + 2));
        $sheet->setCellValue("H" . ($h + 1), 'Eks.');
        $sheet->mergeCells("I{$h}:L{$h}");
        $sheet->setCellValue("I{$h}", 'Asal');
        $sheet->mergeCells("I" . ($h + 1) . ":J" . ($h + 1));
        $sheet->setCellValue("I" . ($h + 1), 'Beli');
        $sheet->mergeCells("K" . ($h + 1) . ":L" . ($h + 1));
        $sheet->setCellValue("K" . ($h + 1), 'hadiah');
        $sheet->setCellValue("I" . ($h + 2), 'Jdl');
        $sheet->setCellValue("J" . ($h + 2), 'Eks.');
        $sheet->setCellValue("K" . ($h + 2), 'Jdl');
        $sheet->setCellValue("L" . ($h + 2), 'Eks.');
        $sheet->mergeCells("M{$h}:V{$h}");
        $sheet->setCellValue("M{$h}", 'Klasifikasi');
        foreach (self::KOLOM_KLAS as $d => $col) {
            $sheet->mergeCells("{$col}" . ($h + 1) . ":{$col}" . ($h + 2));
            $sheet->setCellValueExplicit("{$col}" . ($h + 1), (string) $d, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        }
        $sheet->mergeCells("X{$h}:Z" . ($h + 1));
        $sheet->setCellValue("X{$h}", 'Bahasa');
        $sheet->setCellValue("X" . ($h + 2), 'Ind');
        $sheet->setCellValue("Y" . ($h + 2), 'Ingg');
        $sheet->setCellValue("Z" . ($h + 2), 'Lain');

        $sheet->getStyle("A{$h}:AB" . ($h + 2))->applyFromArray([
            'font' => ['bold' => true, 'size' => 9],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E5E7EB']],
        ]);

        // Data
        $r = $h + 3;
        $awalData = $r;
        $total = BukuInduk::totalAwal();
        $kolomBahasa = ['ind' => 'X', 'ing' => 'Y', 'lain' => 'Z'];
        foreach ($rows as $row) {
            $total = BukuInduk::tambahTotal($total, $row);
            $sheet->setCellValue("A{$r}", $row->no);
            $sheet->setCellValue("B{$r}", $row->tgl);
            $sheet->setCellValue("C{$r}", $row->no_inventori);
            $sheet->setCellValue("D{$r}", $row->pengarang);
            $sheet->setCellValue("E{$r}", $row->judul);
            $sheet->setCellValue("F{$r}", $row->penerbit);
            $sheet->setCellValue("G{$r}", $row->jdl ?: null);
            $sheet->setCellValue("H{$r}", $row->eks);
            $sheet->setCellValue("I{$r}", $row->beli_jdl ?: null);
            $sheet->setCellValue("J{$r}", $row->beli_eks ?: null);
            $sheet->setCellValue("K{$r}", $row->hadiah_jdl ?: null);
            $sheet->setCellValue("L{$r}", $row->hadiah_eks ?: null);
            if ($row->klas !== null) {
                $sheet->setCellValue(self::KOLOM_KLAS[$row->klas] . $r, '√');
            }
            $sheet->setCellValue("W{$r}", $row->call_number);
            $sheet->setCellValue($kolomBahasa[$row->bahasa] . $r, '√');
            $sheet->setCellValue("AA{$r}", $row->harga);
            $sheet->setCellValue("AB{$r}", $row->ket);
            $r++;
        }
        $akhirData = max($awalData, $r - 1);

        // Baris JUMLAH
        $sheet->mergeCells("A{$r}:F{$r}");
        $sheet->setCellValue("A{$r}", 'JUMLAH');
        foreach (['G' => 'jdl', 'H' => 'eks', 'I' => 'beli_jdl', 'J' => 'beli_eks', 'K' => 'hadiah_jdl', 'L' => 'hadiah_eks', 'AA' => 'harga'] as $col => $k) {
            $sheet->setCellValue("{$col}{$r}", $total[$k]);
        }
        $sheet->getStyle("A{$r}:AB{$r}")->getFont()->setBold(true);
        $sheet->getStyle("A{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("A{$h}:AB{$r}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle("A{$awalData}:AB{$akhirData}")->getFont()->setSize(9);
        $sheet->getStyle("A{$awalData}:C{$akhirData}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("G{$awalData}:Z{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("W{$awalData}:W{$akhirData}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle("AA{$awalData}:AA{$r}")->getNumberFormat()->setFormatCode('#,##0');

        // Siap cetak A4 landscape, header tabel berulang tiap halaman
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(PageSetup::PAPERSIZE_A4)
            ->setFitToWidth(1)
            ->setFitToHeight(0)
            ->setRowsToRepeatAtTopByStartAndEnd($h, $h + 2);
        $sheet->getPageMargins()->setLeft(0.3)->setRight(0.3)->setTop(0.5)->setBottom(0.5);
        $sheet->freezePane('A' . ($h + 3));

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $namaFile, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Import file berformat BUKU-INDUK (.xls/.xlsx) menjadi inventori, eksemplar & katalog.
     * Baris judul, header berulang, dan baris JUMLAH otomatis dilewati.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file_buku_induk' => 'required|file|mimes:xls,xlsx|max:20480',
            'mode_nomor' => 'nullable|in:asli,lanjut',
        ], [
            'file_buku_induk.required' => 'File buku induk wajib dipilih.',
            'file_buku_induk.mimes' => 'Format file harus .xls atau .xlsx',
        ]);

        @set_time_limit(600);
        @ini_set('memory_limit', '1024M');

        $modeNomor = $request->input('mode_nomor', 'asli');

        try {
            $spreadsheet = IOFactory::load($request->file('file_buku_induk')->getPathname());
        } catch (\Throwable $e) {
            return back()->with('error', 'File tidak dapat dibaca: ' . $e->getMessage());
        }

        $sheet = $this->cariSheetBukuInduk($spreadsheet);
        if (!$sheet) {
            return back()->with('error', 'Format tidak dikenali. Pastikan file memiliki header "No Inventaris" seperti format BUKU-INDUK.');
        }

        $sekolah = Sekolah::first();
        if (!$sekolah) {
            return back()->with('error', 'Data sekolah belum ada. Isi menu Sekolah terlebih dahulu.');
        }

        // Data referensi
        $jenisMedia = JenisMedia::where('nama_jenis_media', 'like', '%buku%')->first()
            ?? JenisMedia::first() ?? JenisMedia::create(['nama_jenis_media' => 'Buku']);
        $sumber = Sumber::first() ?? Sumber::create(['nama' => 'BOS']);
        $jsBeli = JenisSumber::where('nama_sumber', 'like', '%beli%')->first()
            ?? JenisSumber::create(['nama_sumber' => 'Pembelian']);
        $jsHadiah = JenisSumber::where(function ($q) {
            $q->where('nama_sumber', 'like', '%hibah%')
                ->orWhere('nama_sumber', 'like', '%hadiah%')
                ->orWhere('nama_sumber', 'like', '%sumbangan%');
        })->first() ?? JenisSumber::create(['nama_sumber' => 'Hibah']);

        $indukAda = Eksemplar::pluck('no_induk')->map(fn($n) => (string) $n)->flip()->all();
        $inventarisAda = Eksemplar::pluck('no_inventori')->map(fn($n) => mb_strtolower((string) $n))->flip()->all();
        $nomorBerikut = ((int) DB::table('eksemplar')->selectRaw('MAX(CAST(no_induk AS UNSIGNED)) as m')->value('m')) + 1;

        $berhasil = 0;
        $dilewati = 0;
        $judulBaru = 0;
        $peta = [];       // key judul|pengarang|tanggal => ['inventori'=>..., 'count'=>int]
        $cachePenerbit = [];
        $cacheKategori = [];

        DB::beginTransaction();
        try {
            $baris = $sheet->getHighestDataRow();
            for ($r = 1; $r <= $baris; $r++) {
                $no = $sheet->getCell("A{$r}")->getValue();
                $noInv = trim((string) $sheet->getCell("C{$r}")->getValue());
                $judul = trim((string) $sheet->getCell("E{$r}")->getValue());

                if (!is_numeric($no) || $noInv === '' || $judul === '') {
                    continue; // kop, header, JUMLAH, baris kosong
                }

                $noIndukAsli = str_pad((string) (int) $no, 6, '0', STR_PAD_LEFT);

                if (isset($inventarisAda[mb_strtolower($noInv)])) {
                    $dilewati++;
                    continue;
                }

                if ($modeNomor === 'lanjut' || isset($indukAda[$noIndukAsli])) {
                    if ($modeNomor === 'asli') {
                        $dilewati++;
                        continue;
                    }
                    do {
                        $noInduk = str_pad((string) $nomorBerikut++, 6, '0', STR_PAD_LEFT);
                    } while (isset($indukAda[$noInduk]));
                } else {
                    $noInduk = $noIndukAsli;
                }

                $pengarang = trim((string) $sheet->getCell("D{$r}")->getValue()) ?: '-';
                $penerbitNama = trim((string) $sheet->getCell("F{$r}")->getValue()) ?: '-';
                $tanggal = $this->parseTanggal($sheet->getCell("B{$r}")->getValue());
                $harga = $this->parseHarga($sheet->getCell("AA{$r}")->getCalculatedValue());
                $hadiah = $this->terisi($sheet->getCell("K{$r}")->getCalculatedValue())
                    || $this->terisi($sheet->getCell("L{$r}")->getCalculatedValue());

                $digit = null;
                foreach (self::KOLOM_KLAS as $d => $col) {
                    if ($this->terisi($sheet->getCell("{$col}{$r}")->getValue())) {
                        $digit = $d;
                        break;
                    }
                }
                $callNumber = trim((string) $sheet->getCell("W{$r}")->getValue());
                if ($digit === null) {
                    $digit = BukuInduk::digitKlasifikasi($callNumber);
                }
                $kodeDdc = preg_match('/^\d{3}(\.\d+)?/', $callNumber, $m) ? $m[0] : ($digit !== null ? $digit . '00' : null);

                $bahasa = 'Indonesia';
                if ($this->terisi($sheet->getCell("Y{$r}")->getValue())) {
                    $bahasa = 'Inggris';
                } elseif ($this->terisi($sheet->getCell("Z{$r}")->getValue())) {
                    $bahasa = 'Lainnya';
                }

                $ket = mb_strtolower((string) $sheet->getCell("AB{$r}")->getValue());
                $status = str_contains($ket, 'hilang') ? 'hilang' : (str_contains($ket, 'rusak') ? 'rusak' : 'tersedia');

                $kunci = mb_strtolower($judul . '|' . $pengarang . '|' . $tanggal);

                if (!isset($peta[$kunci])) {
                    $inventori = Inventori::whereRaw('LOWER(judul_buku) = ?', [mb_strtolower($judul)])
                        ->whereRaw('LOWER(pengarang) = ?', [mb_strtolower($pengarang)])
                        ->whereDate('tanggal_pembelian', $tanggal)
                        ->first();

                    if (!$inventori) {
                        $penerbit = $cachePenerbit[mb_strtolower($penerbitNama)]
                            ??= Penerbit::firstOrCreate(['nama_penerbit' => $penerbitNama]);
                        $namaKategori = $digit !== null ? BukuInduk::KLASIFIKASI_DDC[$digit] : 'Umum';
                        $kategori = $cacheKategori[$namaKategori]
                            ??= KategoriBuku::firstOrCreate(['nama_kategori' => $namaKategori]);

                        $inventori = Inventori::create([
                            'id_jenis_media' => $jenisMedia->id,
                            'jumlah_eksemplar' => 0,
                            'tanggal_pembelian' => $tanggal,
                            'id_jenis_sumber' => $hadiah ? $jsHadiah->id : $jsBeli->id,
                            'id_sumber' => $sumber->id,
                            'id_kategori_buku' => $kategori->id,
                            'judul_buku' => $judul,
                            'pengarang' => $pengarang,
                            'id_sekolah' => $sekolah->id,
                            'id_penerbit' => $penerbit->id,
                            'harga_satuan' => $harga,
                            'total_harga' => 0,
                            'bahasa' => $bahasa,
                        ]);

                        Katalog::create([
                            'id_inventori' => $inventori->id,
                            'kategori_buku' => $kategori->nama_kategori,
                            'judul_buku' => $judul,
                            'pengarang' => $pengarang,
                            'penerbit' => $penerbitNama,
                            'kode_ddc' => $kodeDdc,
                            'no_panggil' => $callNumber ?: null,
                        ]);
                        $judulBaru++;
                    }

                    $peta[$kunci] = ['inventori' => $inventori];
                }

                $peta[$kunci]['inventori']->eksemplar()->create([
                    'no_induk' => $noInduk,
                    'no_inventori' => $noInv,
                    'no_rfid' => 'CRS-' . strtoupper(Str::random(6)),
                    'status' => $status,
                ]);

                $indukAda[$noInduk] = true;
                $inventarisAda[mb_strtolower($noInv)] = true;
                $berhasil++;
            }

            // Sinkronkan jumlah eksemplar & total harga
            foreach ($peta as $item) {
                $inv = $item['inventori'];
                $jumlah = $inv->eksemplar()->count();
                $inv->update([
                    'jumlah_eksemplar' => $jumlah,
                    'total_harga' => $jumlah * (float) $inv->harga_satuan,
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Import dibatalkan pada baris ' . ($r ?? '?') . ': ' . $e->getMessage());
        }

        if ($berhasil === 0) {
            return back()->with('error', "Tidak ada data baru yang diimport. $dilewati baris dilewati karena No. Induk / No. Inventaris sudah terdaftar.");
        }

        return back()->with('success', "Import buku induk berhasil: $berhasil eksemplar ($judulBaru judul baru) ditambahkan"
            . ($dilewati ? ", $dilewati baris dilewati karena sudah terdaftar." : '.'));
    }

    private function cariSheetBukuInduk(Spreadsheet $spreadsheet)
    {
        $kandidat = [];
        foreach ($spreadsheet->getWorksheetIterator() as $ws) {
            $maks = min(40, $ws->getHighestDataRow());
            for ($r = 1; $r <= $maks; $r++) {
                if (stripos((string) $ws->getCell("C{$r}")->getValue(), 'inventaris') !== false) {
                    $kandidat[] = $ws;
                    break;
                }
            }
        }
        if (!$kandidat) {
            return null;
        }
        foreach ($kandidat as $ws) {
            if (stripos($ws->getTitle(), 'inventaris') !== false || stripos($ws->getTitle(), 'induk') !== false) {
                return $ws;
            }
        }
        return $kandidat[0];
    }

    private function terisi($nilai): bool
    {
        return $nilai !== null && trim((string) $nilai) !== '' && trim((string) $nilai) !== '0';
    }

    private function parseTanggal($nilai): string
    {
        if (is_numeric($nilai) && $nilai > 20000) {
            return Carbon::instance(ExcelDate::excelToDateTimeObject($nilai))->toDateString();
        }
        $nilai = trim((string) $nilai);
        foreach (['d/m/Y', 'd-m-Y', 'Y-m-d', 'd.m.Y'] as $format) {
            try {
                $tgl = Carbon::createFromFormat('!' . $format, $nilai);
                if ($tgl !== false) {
                    return $tgl->toDateString();
                }
            } catch (\Throwable $e) {
                continue;
            }
        }
        return now()->toDateString();
    }

    private function parseHarga($nilai): float
    {
        if (is_numeric($nilai)) {
            return (float) $nilai;
        }
        $teks = preg_replace('/,-?\s*$/', '', trim((string) $nilai)); // "109.000,-" -> "109.000"
        $teks = preg_replace('/,\d{1,2}$/', '', $teks);               // buang desimal ",00"
        $angka = preg_replace('/[^0-9]/', '', $teks);
        return $angka === '' ? 0 : (float) $angka;
    }
}
