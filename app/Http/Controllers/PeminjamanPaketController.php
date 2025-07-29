<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DetailPeminjamanPaket;
use App\Models\PeminjamanPaket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;


class PeminjamanPaketController extends Controller
{
    public function index(Request $request)
    {
        $activeMenu = "peminjamanPaket";

        $search = $request->input('search');
        $category = $request->input('category', 'all');

        $query = DetailPeminjamanPaket::with(['paketBuku', 'peminjamanPaket.anggota.user'])
            ->whereHas('peminjamanPaket', function ($q) use ($search) {
                $q->where('status', ['menunggu']);

                // Filter berdasarkan NISN jika ada pencarian
                if (!empty($search)) {
                    $q->whereHas('anggota', function ($q2) use ($search) {
                        $q2->where('nisn', 'like', "%{$search}%");
                    });
                }
            });

        // Filter kategori kelas jika ada
        if ($category !== 'all') {
            $query->whereHas('peminjaman.anggota.kelas', function ($q) use ($category) {
                preg_match('/(\d+) (IPA|IPS|Bahasa)/', $category, $match);
                if ($match) {
                    $kelas = $match[1] . ' ' . $match[2];
                    $q->where('nama_kelas', 'like', $kelas . '%');
                }
            });
        }

        $peminjamanPaket = $query->paginate(10)->appends([
            'search' => $search,
            'category' => $category
        ]);

        return view('admin.peminjaman-paket.index', [
            'activeMenu' => $activeMenu,
            'peminjamanPaket' => $peminjamanPaket,
            'category' => $category,
            'search' => $search,
        ]);
    }


    public function updateStatus(Request $request, $id)
    {
        // 1. Validasi aksi
        $validated = $request->validate([
            'aksi' => 'required|in:berhasil,tolak',
        ]);
        $aksiBaru = $validated['aksi'];     // 'berhasil' | 'tolak'

        // 2. Ambil peminjaman beserta detail & paket
        $peminjaman = PeminjamanPaket::with('detailPeminjamanPaket.paketBuku')
            ->findOrFail($id);

        // 3. Cek sudah diproses?
        if ($peminjaman->status !== 'menunggu') {
            return back()->with('warning', 'Peminjaman sudah diproses sebelumnya.');
        }

        DB::beginTransaction();
        try {
            // 4. Update status & petugas yang memproses
            $peminjaman->update([
                'status'  => $aksiBaru,
                'user_id' => Auth::id(),     // petugas login
            ]);

            // 5. Jika ditolak, kembalikan stok
            if ($aksiBaru === 'tolak') {
                foreach ($peminjaman->detailPeminjamanPaket as $detail) {
                    $detail->paketBuku->increment('stok_tersedia');
                }
            }

            DB::commit();
            return back()->with('success', 'Status peminjaman berhasil diperbarui.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui status: ' . $th->getMessage());
        }
    }

    public function laporanPeminjamanPaket(Request $request)
    {
        $activeMenu = "laporanPaket";

        $search = $request->input('search');
        $category = $request->input('category', 'all');
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');

        $query = DetailPeminjamanPaket::with(['paketBuku', 'peminjamanPaket.anggota.user'])
            ->whereHas('peminjamanPaket', function ($q) use ($search, $tanggalMulai, $tanggalSelesai) {
                $q->where('status', 'selesai');

                if (!empty($search)) {
                    $q->whereHas('anggota', function ($q2) use ($search) {
                        $q2->where('nisn', 'like', "%{$search}%");
                    });
                }

                if ($tanggalMulai && $tanggalSelesai) {
                    $q->whereBetween('created_at', [$tanggalMulai . ' 00:00:00', $tanggalSelesai . ' 23:59:59']);
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

        $peminjamanPaket = $query->paginate(10)->appends($request->all());

        return view('admin.laporan.paket', [
            'activeMenu' => $activeMenu,
            'peminjamanPaket' => $peminjamanPaket,
            'category' => $category,
            'search' => $search,
            'tanggalMulai' => $tanggalMulai,
            'tanggalSelesai' => $tanggalSelesai,
        ]);
    }

    public function exportLaporanPaketExcel(Request $request)
    {
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');

        $query = DetailPeminjamanPaket::with([
            'paketBuku',
            'peminjamanPaket.anggota.user',
            'peminjamanPaket.anggota.kelas'
        ])
            ->whereHas('peminjamanPaket', function ($q) use ($tanggalMulai, $tanggalSelesai) {
                $q->where('status', 'selesai');
                if ($tanggalMulai && $tanggalSelesai) {
                    $q->whereBetween('created_at', [
                        $tanggalMulai . ' 00:00:00',
                        $tanggalSelesai . ' 23:59:59'
                    ]);
                }
            });

        $data = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Logo
        $logoPath = public_path('logo-smancir.png');
        if (file_exists($logoPath)) {
            $drawing = new Drawing();
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

        // Kop surat
        $sheet->mergeCells('C1:H1')->setCellValue('C1', 'PEMERINTAH PROVINSI BANTEN');
        $sheet->mergeCells('C2:H2')->setCellValue('C2', 'DINAS PENDIDIKAN DAN KEBUDAYAAN');
        $sheet->mergeCells('C3:H3')->setCellValue('C3', 'UPT SMA NEGERI 1 CIRUAS');
        $sheet->mergeCells('C4:H4')->setCellValue('C4', 'Jalan Raya Jakarta Km 9,5 Serang Telp. 280043');
        $sheet->mergeCells('C5:H5')->setCellValue('C5', 'Web: www.sman1cir.sch.id | Email: ciruas@sman1cir.sch.id');
        $sheet->mergeCells('A7:H7')->setCellValue('A7', 'LAPORAN PEMINJAMAN PAKET');

        if ($tanggalMulai && $tanggalSelesai) {
            $sheet->mergeCells('A8:H8')->setCellValue('A8', 'Periode: ' . $tanggalMulai . ' s.d. ' . $tanggalSelesai);
        } else {
            $sheet->mergeCells('A8:H8')->setCellValue('A8', 'Semua Data');
        }

        // Style kop
        $sheet->getStyle('C1:C5')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle('A7')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);
        $sheet->getStyle('A5:H5')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Header tabel
        $headers = ['No', 'Nama Peminjam', 'NISN', 'Kelas', 'Paket Buku', 'Tanggal'];
        $startRow = 10;
        $sheet->fromArray($headers, null, "A{$startRow}");

        $headerStyle = [
            'font' => ['bold' => true, 'name' => 'Calibri', 'size' => 11],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'BDD7EE'],
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ];
        $sheet->getStyle("A{$startRow}:F{$startRow}")->applyFromArray($headerStyle);

        // Isi Data
        $cellStyle = [
            'font' => ['name' => 'Calibri', 'size' => 11],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ];

        $row = $startRow + 1;
        $no = 1;
        foreach ($data as $item) {
            $sheet->fromArray([
                $no++,
                $item->peminjamanPaket->anggota->user->name ?? '-',
                $item->peminjamanPaket->anggota->nisn ?? '-',
                $item->peminjamanPaket->anggota->kelas->nama_kelas ?? '-',
                $item->paketBuku->nama_paket ?? '-',
                $item->peminjamanPaket->created_at->format('Y-m-d'),
            ], null, "A{$row}");

            $sheet->getStyle("A{$row}:F{$row}")->applyFromArray($cellStyle);
            $row++;
        }

        // Auto width
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Atur ukuran halaman ke F4 (gunakan Legal sebagai pendekatan)
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_PORTRAIT)
            ->setPaperSize(PageSetup::PAPERSIZE_LEGAL);

        // Atur margin halaman
        $sheet->getPageMargins()->setTop(0.5);
        $sheet->getPageMargins()->setBottom(0.5);
        $sheet->getPageMargins()->setLeft(0.5);
        $sheet->getPageMargins()->setRight(0.5);

        // Export
        $writer = new Xlsx($spreadsheet);
        $filename = 'laporan-peminjaman-paket.xlsx';

        ob_start();
        $writer->save('php://output');
        $excelOutput = ob_get_clean();

        return response($excelOutput, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
