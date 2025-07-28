<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BukuTamu;
use App\Models\Anggota;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Models\Kelas;

class BukuTamuController extends Controller
{
    public function create()
    {
        $activeMenu = 'buku-tamu';
        return view('admin.buku-tamu.form', compact('activeMenu'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nisn' => 'nullable|string',
        ]);

        $anggota = Anggota::where('nisn', $request->nisn)->first();

        if ($anggota) {
            // Anggota: keperluan opsional
            BukuTamu::create([
                'anggota_id' => $anggota->id,
                'nisn' => $request->nisn ?? null,
                'nama' => $anggota->user->name, // asumsikan user relasi ada dan ada nama
                'asal_instansi' => null,
                'keperluan' => $request->keperluan ?? null,
            ]);

            return redirect()->back()->with('success', 'Kunjungan anggota berhasil dicatat.');
        } else {
            // Non anggota wajib isi nama, asal_instansi, dan keperluan
            $request->validate([
                'nama' => 'required|string',
                'asal_instansi' => 'required|string',
                'keperluan' => 'required|string',
            ], [
                'nama.required' => 'Nama wajib diisi untuk non anggota.',
                'asal_instansi.required' => 'Asal instansi wajib diisi untuk non anggota.',
                'keperluan.required' => 'Keperluan wajib diisi untuk non anggota.',
            ]);

            BukuTamu::create([
                'anggota_id' => null,
                'nisn' => $request->nisn,
                'nama' => $request->nama,
                'asal_instansi' => $request->asal_instansi,
                'keperluan' => $request->keperluan,
            ]);

            return redirect()->back()->with('success', 'Kunjungan tamu berhasil dicatat.');
        }
    }

    public function LogTamu(Request $request)
    {
        $activeMenu = 'buku-tamu';

        $tanggalMulai = $request->input('tanggal_mulai', now()->startOfMonth()->toDateString());
        $tanggalSelesai = $request->input('tanggal_selesai', now()->toDateString());
        $kelasFilter = $request->input('kelas');

        $query = BukuTamu::with(['anggota.kelas'])
            ->whereBetween('created_at', [$tanggalMulai . ' 00:00:00', $tanggalSelesai . ' 23:59:59']);

        if ($kelasFilter) {
            $query->whereHas('anggota.kelas', function ($q) use ($kelasFilter) {
                $q->where('nama_kelas', 'like', "%$kelasFilter%");
            });
        }

        $bukuTamu = $query->paginate(10);
        $kelasList = Kelas::pluck('nama_kelas')->unique();

        return view('admin.buku-tamu.log-tamu', compact('activeMenu', 'bukuTamu', 'tanggalMulai', 'tanggalSelesai', 'kelasFilter', 'kelasList'));
    }

    public function exportLogTamuExcel(Request $request)
    {
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');
        $kelasFilter = $request->input('kelas');

        $query = BukuTamu::with(['anggota.kelas'])
            ->whereBetween('created_at', [$tanggalMulai . ' 00:00:00', $tanggalSelesai . ' 23:59:59']);

        if ($kelasFilter) {
            $query->whereHas('anggota.kelas', function ($q) use ($kelasFilter) {
                $q->where('nama_kelas', 'like', "%$kelasFilter%");
            });
        }

        $data = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Logo
        $logoPath = public_path('logo-smancir.png');
        if (file_exists($logoPath)) {
            $drawing = new Drawing();
            $drawing->setName('Logo')->setPath($logoPath)->setHeight(100)->setCoordinates('B2')->setOffsetX(10)->setWorksheet($sheet);
            for ($i = 2; $i <= 6; $i++) $sheet->getRowDimension($i)->setRowHeight(20);
        }

        // Kop Surat
        $sheet->mergeCells('C1:H1')->setCellValue('C1', 'PEMERINTAH PROVINSI BANTEN');
        $sheet->mergeCells('C2:H2')->setCellValue('C2', 'DINAS PENDIDIKAN DAN KEBUDAYAAN');
        $sheet->mergeCells('C3:H3')->setCellValue('C3', 'UPT SMA NEGERI 1 CIRUAS');
        $sheet->mergeCells('C4:H4')->setCellValue('C4', 'Jalan Raya Jakarta Km 9,5 Serang Telp. 280043');
        $sheet->mergeCells('C5:H5')->setCellValue('C5', 'Web: www.sman1cir.sch.id | Email: ciruas@sman1cir.sch.id');
        $sheet->mergeCells('A7:H7')->setCellValue('A7', 'LAPORAN BUKU TAMU');

        $sheet->mergeCells('A8:H8')->setCellValue('A8', 'Periode: ' .
            \Carbon\Carbon::parse($tanggalMulai)->format('d-m-Y') .
            ' s.d. ' .
            \Carbon\Carbon::parse($tanggalSelesai)->format('d-m-Y'));

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
                'bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '000000']],
            ],
        ]);

        // Header Tabel
        $headers = ['No', 'NISN', 'Nama', 'Kelas', 'Asal Instansi', 'Keperluan', 'Waktu Kunjungan'];
        $startRow = 10;
        $sheet->fromArray($headers, null, "A{$startRow}");

        $sheet->getStyle("A{$startRow}:G{$startRow}")->applyFromArray([
            'font' => ['bold' => true, 'name' => 'Calibri', 'size' => 11],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'BDD7EE']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        $cellStyle = [
            'font' => ['name' => 'Calibri', 'size' => 11],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];

        $row = $startRow + 1;
        $no = 1;
        foreach ($data as $item) {
            $sheet->fromArray([
                $no++,
                $item->nisn ?? '-',
                $item->nama ?? '-',
                $item->anggota->kelas->nama_kelas ?? '-',
                $item->asal_instansi ?? '-',
                $item->keperluan ?? '-',
                $item->created_at ? $item->created_at->format('d-m-Y H:i') : '-',
            ], null, "A{$row}");

            $sheet->getStyle("A{$row}:G{$row}")->applyFromArray($cellStyle);
            $row++;
        }

        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->getPageSetup()
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT)
            ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_LEGAL);

        $writer = new Xlsx($spreadsheet);
        $filename = 'laporan-log-buku-tamu.xlsx';
        ob_start();
        $writer->save('php://output');
        $excelOutput = ob_get_clean();

        return response($excelOutput, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
