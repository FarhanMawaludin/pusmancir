<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventori;
use App\Models\Eksemplar;
use App\Models\JenisMedia;
use App\Models\JenisSumber;
use App\Models\Sumber;
use App\Models\KategoriBuku;
use App\Models\Sekolah;
use App\Models\Penerbit;
use App\Models\Katalog;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Font;
use Illuminate\Support\Facades\Response;


class InventoriController extends Controller
{
    public function index(Request $request)
    {
        $activeMenu = "inventori";

        $search = $request->input('search');
        $category = $request->input('category', 'all');

        $query = Inventori::with(['eksemplar' => function ($q) {
            $q->orderBy('created_at'); // urutan eksemplar
        }, 'penerbit'])
            ->orderBy('created_at', 'desc');

        if ($search) {
            if ($category === 'judul_buku' || $category === 'all') {
                $query->where('judul_buku', 'like', "%{$search}%");
            }

            if ($category === 'tanggal_pembelian' || $category === 'all') {
                $query->orWhere('tanggal_pembelian', 'like', "%{$search}%");
            }
        }

        // misal id_kategori_buku sesuai kebutuhan, sesuaikan kalau perlu
        if ($category !== 'all' && in_array($category, ['judul_buku', 'tanggal_pembelian']) === false) {
            $query->where('id_kategori_buku', $category);
        }

        $inventori = $query->paginate(10)->appends([
            'search' => $search,
            'category' => $category,
        ]);

        return view('admin.inventori.index', compact('inventori', 'activeMenu', 'search', 'category'));
    }



    public function create()
    {
        $activeMenu = "inventori";

        $sumber = Sumber::all();
        // dd($sumber);
        return view('admin.inventori.create', [
            'jenisMedia'     => JenisMedia::all(),
            'jenisSumber'    => JenisSumber::all(),
            'daftarSumber'         => $sumber,
            'kategoriBuku'   => KategoriBuku::all(),
            'sekolah'        => Sekolah::all(),
            'penerbit'       => Penerbit::all(),
            'activeMenu'     => $activeMenu
        ]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_jenis_media'     => 'required|exists:jenis_media,id',
            'jumlah_eksemplar'   => 'required|integer|min:1',
            'tanggal_pembelian'  => 'required|date',
            'id_jenis_sumber'    => 'required|exists:jenis_sumber,id',
            'id_sumber'          => 'required|exists:sumber,id',
            'id_kategori_buku'   => 'required|exists:kategori_buku,id',
            'judul_buku'         => 'required|string|max:255',
            'pengarang'          => 'required|string|max:255',
            'id_sekolah'         => 'required|exists:sekolah,id',
            'id_penerbit'        => 'required|exists:penerbit,id',
            'harga_satuan'       => 'required|numeric|min:0',
        ]);

        $validated['total_harga'] = $validated['harga_satuan'] * $validated['jumlah_eksemplar'];

        DB::transaction(function () use ($validated) {
            $inventori = Inventori::create($validated);

            Katalog::create([
                'id_inventori'   => $inventori->id,
                'kategori_buku'  => optional($inventori->kategoriBuku)->nama_kategori,
                'judul_buku'     => $inventori->judul_buku,
                'pengarang'      => $inventori->pengarang,
                'penerbit'       => optional($inventori->penerbit)->nama_penerbit,
                'no_panggil'     => null,
                'ringkasan_buku' => null,
                'kode_ddc'       => null,
                'ISBN'           => null,
                'cover_buku'     => null,
            ]);

            $sekolah = $inventori->sekolah;
            $jenisSumber = $inventori->jenisSumber;

            $kodeSekolah = strtoupper($sekolah->kode_sekolah ?? 'XXX');
            $tahun = date('Y', strtotime($inventori->tanggal_pembelian));
            $jenisPembelian = strtoupper(substr($jenisSumber->nama_sumber ?? 'X', 0, 1));

            $lastInduk = DB::table('eksemplar')
                ->selectRaw('MAX(CAST(no_induk AS UNSIGNED)) as max_no')
                ->value('max_no') ?? 0;

            $nextInduk = $lastInduk + 1;
            $padLength = max(6, strlen((string)($nextInduk + $validated['jumlah_eksemplar'])));

            for ($i = 0; $i < $inventori->jumlah_eksemplar; $i++) {
                $angka = $nextInduk + $i;
                $noInduk = str_pad($angka, $padLength, '0', STR_PAD_LEFT);

                $noInventori = "{$noInduk}/{$kodeSekolah}/{$jenisPembelian}/{$tahun}";

                $inventori->eksemplar()->create([
                    'no_induk'     => $noInduk,
                    'no_inventori' => $noInventori,
                    'no_rfid'      => 'CRS-' . strtoupper(Str::random(6)),
                    'status'       => 'tersedia',
                ]);
            }
        });

        return redirect()->route('admin.inventori.index')
            ->with('success', 'Inventori, eksemplar, dan katalog berhasil disimpan.');
    }

    public function show($id)
    {
        $activeMenu = "inventori";
        $inventori = Inventori::with(['eksemplar', 'penerbit', 'sekolah', 'kategoriBuku', 'jenisMedia', 'jenisSumber', 'sumber'])
            ->findOrFail($id);

        return view('admin.inventori.show', compact('inventori', 'activeMenu'));
    }

    public function edit($id)
    {
        $activeMenu = "inventori";

        $inventori = Inventori::with(['eksemplar'])->findOrFail($id);

        return view('admin.inventori.edit', [
            'inventori'      => $inventori,
            'jenisMedia'     => JenisMedia::all(),
            'jenisSumber'    => JenisSumber::all(),
            'daftarSumber'   => Sumber::all(),
            'kategoriBuku'   => KategoriBuku::all(),
            'sekolah'        => Sekolah::all(),
            'penerbit'       => Penerbit::all(),
            'activeMenu'     => $activeMenu,
        ]);
    }


    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'id_jenis_media'     => 'required|exists:jenis_media,id',
            'jumlah_eksemplar'   => 'required|integer|min:1',
            'tanggal_pembelian'  => 'required|date',
            'id_jenis_sumber'    => 'required|exists:jenis_sumber,id',
            'id_sumber'          => 'required|exists:sumber,id',
            'id_kategori_buku'   => 'required|exists:kategori_buku,id',
            'judul_buku'         => 'required|string|max:255',
            'pengarang'          => 'required|string|max:255',
            'id_sekolah'         => 'required|exists:sekolah,id',
            'id_penerbit'        => 'required|exists:penerbit,id',
            'harga_satuan'       => 'required|numeric|min:0',
        ]);

        $validated['total_harga'] = $validated['harga_satuan'] * $validated['jumlah_eksemplar'];

        DB::transaction(function () use ($validated, $id) {
            $inventori = Inventori::with('eksemplar', 'sekolah', 'jenisSumber', 'kategoriBuku', 'penerbit')->findOrFail($id);
            $oldJumlah = $inventori->jumlah_eksemplar;
            $newJumlah = $validated['jumlah_eksemplar'];

            // Update inventori
            $inventori->update($validated);

            // Segarkan relasi agar kategori/penerbit terbaru bisa diakses
            $inventori->refresh();

            // Update katalog
            $katalog = Katalog::where('id_inventori', $inventori->id)->first();
            if ($katalog) {
                $katalog->update([
                    'kategori_buku'  => optional($inventori->kategoriBuku)->nama_kategori,
                    'judul_buku'     => $inventori->judul_buku,
                    'pengarang'      => $inventori->pengarang,
                    'penerbit'       => optional($inventori->penerbit)->nama_penerbit,
                ]);
            }

            // Ambil data sekolah & jenis sumber
            $sekolah = $inventori->sekolah;
            $jenisSumber = $inventori->jenisSumber;

            $kodeSekolah = strtoupper($sekolah->kode_sekolah ?? 'XXX');
            $tahun = date('Y', strtotime($inventori->tanggal_pembelian));
            $jenisPembelian = strtoupper(substr($jenisSumber->nama_sumber ?? 'X', 0, 1));

            if ($newJumlah > $oldJumlah) {
                // TAMBAH EKSEMPLAR
                $selisih = $newJumlah - $oldJumlah;

                $lastInduk = DB::table('eksemplar')
                    ->selectRaw('MAX(CAST(no_induk AS UNSIGNED)) as max_no')
                    ->value('max_no') ?? 0;

                $nextInduk = $lastInduk + 1;
                $padLength = max(6, strlen((string)($nextInduk + $selisih)));

                for ($i = 0; $i < $selisih; $i++) {
                    $angka = $nextInduk + $i;
                    $noInduk = str_pad($angka, $padLength, '0', STR_PAD_LEFT);
                    $noInventori = "{$noInduk}/{$kodeSekolah}/{$jenisPembelian}/{$tahun}";

                    $inventori->eksemplar()->create([
                        'no_induk'     => $noInduk,
                        'no_inventori' => $noInventori,
                        'no_rfid'      => 'RFID-' . Str::uuid(),
                        'status'       => 'tersedia',
                    ]);
                }
            } elseif ($newJumlah < $oldJumlah) {
                // HAPUS EKSEMPLAR TERAKHIR YANG TERSEDIA
                $selisih = $oldJumlah - $newJumlah;

                $eksemplarTersedia = $inventori->eksemplar()
                    ->where('status', 'tersedia')
                    ->orderByDesc('created_at')
                    ->take($selisih)
                    ->get();

                if ($eksemplarTersedia->count() < $selisih) {
                    throw new \Exception("Tidak cukup eksemplar yang tersedia untuk dihapus.");
                }

                foreach ($eksemplarTersedia as $eksemplar) {
                    $eksemplar->delete();
                }
            }
        });

        return redirect()->route('admin.inventori.index')
            ->with('success', 'Inventori dan katalog berhasil diperbarui.');
    }




    public function destroy($id)
    {
        $inventori = Inventori::with('eksemplar')->findOrFail($id);

        // Cek apakah ada eksemplar yang sedang dipinjam
        $sedangDipinjam = $inventori->eksemplar()->where('status', 'dipinjam')->exists();

        if ($sedangDipinjam) {
            return redirect()->route('admin.inventori.index')
                ->with('error', 'Inventori tidak dapat dihapus karena ada eksemplar yang sedang dipinjam.');
        }

        // Jika tidak ada yang dipinjam, hapus semua eksemplar dulu (optional tergantung constraint DB)
        $inventori->eksemplar()->delete();

        // Hapus inventori
        $inventori->delete();

        return redirect()->route('admin.inventori.index')
            ->with('success', 'Inventori berhasil dihapus.');
    }

    /**
     * Export laporan inventori per eksemplar (dengan kolom Jumlah Judul & Jumlah Eksemplar di paling kanan).
     */
    public function export()
    {
        /* =============================================================
         * 1. Ambil data inventori + eksemplar
         * ===========================================================*/
        $data = DB::table('eksemplar')
            ->join('inventori', 'eksemplar.id_inventori', '=', 'inventori.id')
            ->join('sekolah',   'inventori.id_sekolah',  '=', 'sekolah.id')
            ->join('penerbit',  'inventori.id_penerbit', '=', 'penerbit.id')
            ->join('kategori_buku', 'inventori.id_kategori_buku', '=', 'kategori_buku.id')
            ->join('jenis_sumber',  'inventori.id_jenis_sumber',  '=', 'jenis_sumber.id')
            ->leftJoin('katalog',   'katalog.id_inventori', '=', 'inventori.id')
            ->select(
                'inventori.id as id_inventori',
                'eksemplar.no_induk',
                'eksemplar.no_inventori',
                'inventori.judul_buku',
                'inventori.pengarang',
                'penerbit.nama_penerbit',
                'kategori_buku.nama_kategori',
                'inventori.tanggal_pembelian',
                'inventori.harga_satuan',
                'jenis_sumber.nama_sumber as jenis_sumber',
                'katalog.no_panggil'
            )
            ->orderBy('eksemplar.no_induk')
            ->get();

        /* =============================================================
         * 2. Inisialisasi Spreadsheet
         * ===========================================================*/
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        /* =============================================================
         * 3. Logo + Kop Surat
         * ===========================================================*/
        $logoPath = public_path('logo-smancir.png');            // JANGAN DIHILANGKAN
        if (file_exists($logoPath)) {
            $drawing = new Drawing();
            $drawing->setName('Logo')
                    ->setDescription('Logo Sekolah')
                    ->setPath($logoPath)
                    ->setHeight(100)
                    ->setCoordinates('B2')
                    ->setOffsetX(10)
                    ->setWorksheet($sheet);

            // Tinggi baris kop
            for ($i = 2; $i <= 6; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(20);
            }
        }

        // Teks kop
        $sheet->mergeCells('C1:M1')->setCellValue('C1', 'PEMERINTAH PROVINSI BANTEN');
        $sheet->mergeCells('C2:M2')->setCellValue('C2', 'DINAS PENDIDIKAN DAN KEBUDAYAAN');
        $sheet->mergeCells('C3:M3')->setCellValue('C3', 'UPT SMA NEGERI 1 CIRUAS');
        $sheet->mergeCells('C4:M4')->setCellValue('C4', 'Jalan Raya Jakarta Km 9,5 Serang Telp. 280043');
        $sheet->mergeCells('C5:M5')->setCellValue('C5', 'Situs web: http://www.sman1cir.sch.id | Email: ciruas@sman1cir.sch.id');
        $sheet->mergeCells('A7:M7')->setCellValue('A7', 'BUKU INVENTARIS/INDUK');

        $sheet->getStyle('C1:C5')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle('A7')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 11],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);
        $sheet->getStyle('A5:M5')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color'       => ['rgb' => '000000'],
                ],
            ],
        ]);

        /* =============================================================
         * 4. Header Tabel (kolom L & M = Jumlah Judul & Jumlah Eksemplar)
         * ===========================================================*/
        $headers = [
            'No', 'No Induk', 'No Inventori', 'Judul Buku', 'Pengarang',
            'Penerbit', 'Kategori', 'Tanggal Pembelian', 'Harga Satuan',
            'Jenis Sumber', 'No Panggil',
            'Jumlah Judul',          // L
            'Jumlah Eksemplar'       // M
        ];
        $startRow = 9;
        $sheet->fromArray($headers, null, "A{$startRow}");

        $headerStyle = [
            'font' => ['bold' => true, 'name' => 'Calibri', 'size' => 11],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'BDD7EE'],
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ];
        $sheet->getStyle("A{$startRow}:M{$startRow}")->applyFromArray($headerStyle);

        /* =============================================================
         * 5. Isi Data
         * ===========================================================*/
        $cellStyle = [
            'font' => ['name' => 'Calibri', 'size' => 11],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ];

        $grouped        = collect($data)->groupBy('id_inventori');
        $row            = $startRow + 1;
        $no             = 1;
        $totalJudul     = 0;
        $totalEksemplar = 0;

        foreach ($grouped as $group) {
            $jumlahBaris = $group->count();      // eksemplar per judul
            $totalJudul++;
            $totalEksemplar += $jumlahBaris;

            foreach ($group as $item) {
                $sheet->fromArray([
                    $no,
                    $item->no_induk,
                    $item->no_inventori,
                    $item->judul_buku,
                    $item->pengarang,
                    $item->nama_penerbit,
                    $item->nama_kategori,
                    $item->tanggal_pembelian,
                    'Rp. '.number_format($item->harga_satuan, 0, ',', '.'),
                    $item->jenis_sumber,
                    $item->no_panggil,
                    '',   // L (Jumlah Judul)
                    '',   // M (Jumlah Eksemplar)
                ], null, "A{$row}");

                $sheet->getStyle("A{$row}:M{$row}")->applyFromArray($cellStyle);
                $row++;
                $no++;
            }

            /* ===== Merge & Isi kolom L & M ===== */
            $mergeStart = $row - $jumlahBaris;

            // L = Jumlah Judul (selalu 1)
            $sheet->setCellValue("L{$mergeStart}", 1);
            if ($jumlahBaris > 1) {
                $sheet->mergeCells("L{$mergeStart}:L".($mergeStart + $jumlahBaris - 1));
            }
            $sheet->getStyle("L{$mergeStart}")->getAlignment()
                  ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                  ->setVertical(Alignment::VERTICAL_CENTER);

            // M = Jumlah Eksemplar
            $sheet->setCellValue("M{$mergeStart}", $jumlahBaris);
            if ($jumlahBaris > 1) {
                $sheet->mergeCells("M{$mergeStart}:M".($mergeStart + $jumlahBaris - 1));
            }
            $sheet->getStyle("M{$mergeStart}")->getAlignment()
                  ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                  ->setVertical(Alignment::VERTICAL_CENTER);
        }

        /* =============================================================
         * 6. Baris Total
         * ===========================================================*/
        $sheet->setCellValue("K{$row}", 'TOTAL');
        $sheet->setCellValue("L{$row}", $totalJudul);
        $sheet->setCellValue("M{$row}", $totalEksemplar);

        $sheet->getStyle("K{$row}:M{$row}")->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ]);

        /* =============================================================
         * 7. Auto‑size kolom
         * ===========================================================*/
        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        /* =============================================================
         * 8. Export ke XLSX
         * ===========================================================*/
        $writer   = new Xlsx($spreadsheet);
        $filename = 'laporan-inventori-per-eksemplar.xlsx';

        ob_start();
        $writer->save('php://output');
        $excelOutput = ob_get_clean();

        return response($excelOutput, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}


