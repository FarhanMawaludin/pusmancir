<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\DetailPeminjaman;
use App\Models\Eksemplar;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PeminjamanController extends Controller
{
    // public function index(Request $request)
    // {
    //     $activeMenu = "peminjaman";

    //     $search = $request->input('search');
    //     $category = $request->input('category', 'all');

    //     $query = DetailPeminjaman::with(['eksemplar', 'peminjaman.anggota.user'])
    //         ->whereHas('peminjaman', function ($q) use ($search) {
    //             $q->where('status', 'menunggu');

    //             // Filter berdasarkan NISN jika ada pencarian
    //             if (!empty($search)) {
    //                 $q->whereHas('anggota', function ($q2) use ($search) {
    //                     $q2->where('nisn', 'like', "%{$search}%");
    //                 });
    //             }
    //         });

    //     // Filter kategori kelas jika ada
    //     if ($category !== 'all') {
    //         $query->whereHas('peminjaman.anggota.kelas', function ($q) use ($category) {
    //             preg_match('/(\d+) (IPA|IPS|Bahasa)/', $category, $match);
    //             if ($match) {
    //                 $kelas = $match[1] . ' ' . $match[2];
    //                 $q->where('nama_kelas', 'like', $kelas . '%');
    //             }
    //         });
    //     }

    //     $peminjaman = $query->paginate(10)->appends([
    //         'search' => $search,
    //         'category' => $category
    //     ]);

    //     return view('admin.peminjaman.index', [
    //         'activeMenu' => $activeMenu,
    //         'peminjaman' => $peminjaman,
    //         'category' => $category,
    //         'search' => $search,
    //     ]);
    // }

    public function index(Request $request)
    {
        try {
            $activeMenu = "peminjaman";

            $search = $request->input('search');
            $category = $request->input('category', 'all');

            $query = DetailPeminjaman::with(['eksemplar', 'peminjaman.anggota.user'])
                ->whereHas('peminjaman', function ($q) use ($search) {
                    $q->where('status', 'menunggu');

                    if (!empty($search)) {
                        $q->whereHas('anggota', function ($q2) use ($search) {
                            $q2->where('nisn', 'like', "%{$search}%");
                        });
                    }
                });

            if ($category !== 'all') {
                $query->whereHas('peminjaman.anggota.kelas', function ($q) use ($category) {
                    preg_match('/(\d+) (IPA|IPS|Bahasa)/', $category, $match);
                    if ($match) {
                        $kelas = $match[1] . ' ' . $match[2];
                        $q->where('nama_kelas', 'like', $kelas . '%');
                    }
                });
            }

            $peminjaman = $query->paginate(10)->appends([
                'search' => $search,
                'category' => $category
            ]);

            return view('admin.peminjaman.index', [
                'activeMenu' => $activeMenu,
                'peminjaman' => $peminjaman,
                'category' => $category,
                'search' => $search,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }





    public function store(Request $request)
    {
        $request->validate([
            'anggota_id'      => 'required|exists:anggota,nisn',
            'eksemplar_id'    => 'required|string|exists:eksemplar,no_rfid',
            'tanggal_pinjam'  => 'required|date',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
        ]);

        // ── Lookup awal ───────────────────────
        $anggota = \App\Models\Anggota::where('nisn', $request->anggota_id)
            ->where('status', 'aktif') // hanya anggota aktif yang boleh meminjam
            ->first();

        $eksemplar = \App\Models\Eksemplar::where('no_rfid', trim($request->eksemplar_id))->first();

        if (!$anggota || !$eksemplar) {
            return back()->withInput()
                ->with('error', 'Anggota tidak aktif atau Eksemplar tidak ditemukan.');
        }

        if ($eksemplar->status === 'dipinjam') {
            return back()->withInput()
                ->with('warning', "Buku dengan RFID '{$request->eksemplar_id}' sedang dipinjam.");
        }

        // ── Tentukan status awal berdasarkan role ─
        $role   = Auth::user()->role;
        $status = in_array($role, ['admin', 'pustakawan']) ? 'berhasil' : 'menunggu';

        DB::beginTransaction();
        try {
            $peminjaman = \App\Models\Peminjaman::create([
                'anggota_id'     => $anggota->id,
                'user_id'        => Auth::id(),
                'tanggal_pinjam' => $request->tanggal_pinjam,
                'tanggal_kembali' => $request->tanggal_kembali,
                'status'         => $status,
            ]);

            \App\Models\DetailPeminjaman::create([
                'peminjaman_id' => $peminjaman->id,
                'eksemplar_id'  => $eksemplar->id,
            ]);

            if ($status === 'berhasil') {
                $eksemplar->update(['status' => 'dipinjam']);
            }

            DB::commit();
            return redirect()->route('admin.peminjaman.index')
                ->with('success', 'Data peminjaman berhasil disimpan.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Gagal menyimpan data: ' . $th->getMessage());
        }
    }


    public function updateStatus(Request $request, $id)
    {
        $request->validate(['aksi' => 'required|in:berhasil,tolak']);

        DB::beginTransaction();
        try {
            $pinjam = Peminjaman::with('detailPeminjaman.eksemplar')->findOrFail($id);

            if ($pinjam->status !== 'menunggu') {
                return back()->with('warning', 'Status sudah diproses.');
            }

            // update status + catat id petugas
            $pinjam->update([
                'status'  => $request->aksi,
                'user_id' => Auth::id(),             // ← petugas yang memproses
            ]);

            if ($request->aksi === 'berhasil') {
                foreach ($pinjam->detailPeminjaman as $detail) {
                    $detail->eksemplar->update(['status' => 'dipinjam']);
                }
            }

            DB::commit();
            return back()->with('success', 'Status berhasil diperbarui.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $th->getMessage());
        }
    }




    //     public function store(Request $request)
    // {
    //     $noRfid = trim($request->eksemplar_id); // bukan [0]
    //     $eksemplar = \App\Models\Eksemplar::where('no_rfid', $noRfid)->first();

    //     dd([
    //         'auth_user_id'     => Auth::id(),
    //         'anggota_id'       => $request->anggota_id,
    //         'tanggal_pinjam'   => $request->tanggal_pinjam,
    //         'tanggal_kembali' => $request->tanggal_kembali,
    //         'rfid_dikirim'     => $noRfid,
    //         'eksemplar_found'  => $eksemplar ? $eksemplar->id : 'Not Found',
    //     ]);
    // }

    public function laporanPeminjamanNonPaket(Request $request)
    {
        try {
            $activeMenu = "laporanPaket";

            $tanggalMulai   = $request->input('tanggal_mulai');
            $tanggalSelesai = $request->input('tanggal_selesai');

            $query = DetailPeminjaman::with(['eksemplar.inventori', 'peminjaman.anggota.user'])
                ->whereHas('peminjaman', function ($q) use ($tanggalMulai, $tanggalSelesai) {
                    $q->whereIn('status', ['selesai']);

                    if ($tanggalMulai && $tanggalSelesai) {
                        $q->whereBetween('tanggal_pinjam', [
                            $tanggalMulai . ' 00:00:00',
                            $tanggalSelesai . ' 23:59:59'
                        ]);
                    }
                });

            $peminjaman = $query->paginate(10)->appends([
                'tanggal_mulai'   => $tanggalMulai,
                'tanggal_selesai' => $tanggalSelesai,
            ]);

            return view('admin.laporan.non-paket', [
                'activeMenu'      => $activeMenu,
                'peminjaman'      => $peminjaman,
                'tanggal_mulai'   => $tanggalMulai,
                'tanggal_selesai' => $tanggalSelesai,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function exportLaporanNonPaketExcel(Request $request)
    {
        $tanggalMulai   = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');

        $query = DetailPeminjaman::with(['eksemplar.inventori', 'peminjaman.anggota.user', 'peminjaman.anggota.kelas'])
            ->whereHas('peminjaman', function ($q) use ($tanggalMulai, $tanggalSelesai) {
                $q->whereIn('status', ['selesai']);

                if ($tanggalMulai && $tanggalSelesai) {
                    $q->whereBetween('tanggal_pinjam', [
                        $tanggalMulai . ' 00:00:00',
                        $tanggalSelesai . ' 23:59:59'
                    ]);
                }
            });

        $data = $query->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // ==================== Logo ====================
        $logoPath = public_path('logo-smancir.png');
        if (file_exists($logoPath)) {
            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setName('Logo')
                ->setDescription('Logo Sekolah')
                ->setPath($logoPath)
                ->setHeight(100)
                ->setCoordinates('B2')
                ->setOffsetX(10)
                ->setWorksheet($sheet);

            for ($i = 2; $i <= 6; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(20);
            }
        }

        // ==================== Kop Surat ====================
        $sheet->mergeCells('C1:H1')->setCellValue('C1', 'PEMERINTAH PROVINSI BANTEN');
        $sheet->mergeCells('C2:H2')->setCellValue('C2', 'DINAS PENDIDIKAN DAN KEBUDAYAAN');
        $sheet->mergeCells('C3:H3')->setCellValue('C3', 'UPT SMA NEGERI 1 CIRUAS');
        $sheet->mergeCells('C4:H4')->setCellValue('C4', 'Jalan Raya Jakarta Km 9,5 Serang Telp. 280043');
        $sheet->mergeCells('C5:H5')->setCellValue('C5', 'Web: www.sman1cir.sch.id | Email: ciruas@sman1cir.sch.id');

        $sheet->mergeCells('A7:H7')->setCellValue('A7', 'LAPORAN PEMINJAMAN NON PAKET');

        if ($tanggalMulai && $tanggalSelesai) {
            $sheet->mergeCells('A8:H8')->setCellValue('A8', 'Periode: ' . $tanggalMulai . ' s.d. ' . $tanggalSelesai);
        } else {
            $sheet->mergeCells('A8:H8')->setCellValue('A8', 'Semua Data');
        }

        // Style kop surat
        $sheet->getStyle('C1:C5')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->getStyle('A7')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT],
        ]);

        $sheet->getStyle('A5:H5')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // ==================== Header Tabel ====================
        $headers = ['No', 'Nama Peminjam', 'NISN', 'Kelas', 'Judul Buku', 'Tanggal Pinjam', 'Tanggal Kembali', 'Status'];
        $startRow = 10;
        $sheet->fromArray($headers, null, "A{$startRow}");

        $headerStyle = [
            'font' => ['bold' => true, 'name' => 'Calibri', 'size' => 11],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'fill' => [
                'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'BDD7EE'],
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
            ],
        ];
        $sheet->getStyle("A{$startRow}:H{$startRow}")->applyFromArray($headerStyle);

        // ==================== Isi Data ====================
        $cellStyle = [
            'font' => ['name' => 'Calibri', 'size' => 11],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
            ],
        ];

        $row = $startRow + 1;
        $no = 1;
        foreach ($data as $item) {
            $sheet->fromArray([
                $no++,
                $item->peminjaman->anggota->user->name ?? '-',
                $item->peminjaman->anggota->nisn ?? '-',
                $item->peminjaman->anggota->kelas->nama_kelas ?? '-',
                $item->eksemplar->inventori->judul_buku ?? '-',
                $item->peminjaman->tanggal_pinjam ? \Carbon\Carbon::parse($item->peminjaman->tanggal_pinjam)->format('d-m-Y') : '-',
                $item->peminjaman->tanggal_kembali ? \Carbon\Carbon::parse($item->peminjaman->tanggal_kembali)->format('d-m-Y') : '-',
                ucfirst($item->peminjaman->status),
            ], null, "A{$row}");

            $sheet->getStyle("A{$row}:H{$row}")->applyFromArray($cellStyle);
            $row++;
        }

        // ==================== Auto-size kolom ====================
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // ==================== Ukuran Kertas F4 (Legal) ====================
        $sheet->getPageSetup()
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT)
            ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_LEGAL);

        // ==================== Download ====================
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'laporan-non-paket.xlsx';

        ob_start();
        $writer->save('php://output');
        $excelOutput = ob_get_clean();

        return response($excelOutput, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    
}
