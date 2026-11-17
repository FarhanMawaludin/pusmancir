<?php

namespace App\Http\Controllers;

use App\Models\DetailPeminjaman;
use App\Models\Kelas;
use App\Services\StatistikPeminjaman;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Laporan peminjaman buku non paket:
 *  - Laporan Jumlah Buku Dipinjam (judul & eksemplar)
 *  - Laporan Data Peminjam & Buku yang dipinjam (sedang / selesai)
 */
class LaporanController extends Controller
{
    /* ------------------------------------------------------------------
     |  LAPORAN JUMLAH BUKU DIPINJAM
     * -----------------------------------------------------------------*/

    private function dataJumlahBuku(Request $request): array
    {
        $tahun = $request->input('tahun', (string) date('Y'));
        $tahun = $tahun === 'semua' ? null : (int) $tahun;
        $bulan = $tahun && $request->filled('bulan') ? (int) $request->input('bulan') : null;

        [$dari, $sampai] = StatistikPeminjaman::rentang($tahun, $bulan);
        $rows = StatistikPeminjaman::rows($dari, $sampai);

        $periode = !$tahun ? 'Semua Tahun'
            : ($bulan ? StatistikPeminjaman::BULAN[$bulan] . ' ' . $tahun : 'Tahun ' . $tahun);

        $rekap = StatistikPeminjaman::perPeriode($rows, $tahun, $bulan);
        if ($bulan) {
            // per hari: tampilkan hanya hari yang ada transaksinya
            $rekap = array_values(array_filter($rekap, fn($r) => $r['eks_total'] > 0));
        }

        return [
            'tahun' => $tahun,
            'bulan' => $bulan,
            'periode' => $periode,
            'granularitas' => !$tahun ? 'Tahun' : ($bulan ? 'Tanggal' : 'Bulan'),
            'ringkasan' => StatistikPeminjaman::ringkasan($rows),
            'rekap' => $rekap,
            'perKategori' => StatistikPeminjaman::perKategori($rows),
            'perKelompok' => StatistikPeminjaman::perKelompokPeminjam($rows),
            'perJudul' => StatistikPeminjaman::perJudul($rows),
        ];
    }

    public function jumlahBuku(Request $request)
    {
        $data = $this->dataJumlahBuku($request);

        return view('admin.laporan.jumlah-buku', $data + [
            'activeMenu' => 'laporanPaket',
            'daftarTahun' => StatistikPeminjaman::daftarTahun(),
            'daftarBulan' => StatistikPeminjaman::BULAN,
        ]);
    }

    public function jumlahBukuPdf(Request $request)
    {
        @set_time_limit(300);
        $data = $this->dataJumlahBuku($request);

        $pdf = Pdf::loadView('admin.laporan.pdf.jumlah-buku', $data)->setPaper('a4', 'portrait');
        $nama = 'laporan-jumlah-buku-dipinjam-' . Str::slug($data['periode']) . '.pdf';

        return $request->boolean('download') ? $pdf->download($nama) : $pdf->stream($nama);
    }

    /* ------------------------------------------------------------------
     |  LAPORAN DATA PEMINJAM & BUKU YANG DIPINJAM
     * -----------------------------------------------------------------*/

    private function queryPeminjam(Request $request)
    {
        $query = DetailPeminjaman::query()
            ->select('detail_peminjaman.*')
            ->join('peminjaman', 'peminjaman.id', '=', 'detail_peminjaman.peminjaman_id')
            ->join('anggota', 'anggota.id', '=', 'peminjaman.anggota_id')
            ->join('users', 'users.id', '=', 'anggota.user_id')
            ->whereIn('peminjaman.status', ['berhasil', 'selesai'])
            ->with([
                'eksemplar.inventori.penerbit',
                'peminjaman.anggota.user',
                'peminjaman.anggota.kelas',
            ]);

        $today = now()->toDateString();

        switch ($request->input('status')) {
            case 'sedang':
                $query->where('peminjaman.status', 'berhasil')->whereNull('detail_peminjaman.tanggal_kembali_asli');
                break;
            case 'terlambat':
                $query->where('peminjaman.status', 'berhasil')->whereNull('detail_peminjaman.tanggal_kembali_asli')
                    ->whereDate('peminjaman.tanggal_kembali', '<', $today);
                break;
            case 'selesai':
                $query->where(function ($q) {
                    $q->whereNotNull('detail_peminjaman.tanggal_kembali_asli')
                        ->orWhere('peminjaman.status', 'selesai');
                });
                break;
        }

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('peminjaman.tanggal_pinjam', '>=', $request->input('tanggal_mulai'));
        }
        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('peminjaman.tanggal_pinjam', '<=', $request->input('tanggal_selesai'));
        }

        if ($request->input('jenis') === 'guru') {
            $query->where('anggota.jenis_anggota', 'guru');
        } elseif ($request->input('jenis') === 'siswa') {
            $query->where(fn($q) => $q->where('anggota.jenis_anggota', '!=', 'guru')->orWhereNull('anggota.jenis_anggota'));
        }

        if ($request->filled('kelas_id')) {
            $query->where('anggota.kelas_id', $request->input('kelas_id'));
        }

        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                    ->orWhere('anggota.nisn', 'like', "%{$search}%")
                    ->orWhereHas('eksemplar.inventori', fn($q2) => $q2->where('judul_buku', 'like', "%{$search}%"));
            });
        }

        return $query->orderBy('users.name')
            ->orderBy('peminjaman.tanggal_pinjam')
            ->orderBy('detail_peminjaman.id');
    }

    /** Status satu eksemplar yang dipinjam: sedang | terlambat | selesai */
    public static function statusDetail($detail): string
    {
        $p = $detail->peminjaman;
        if ($detail->tanggal_kembali_asli || $p->status === 'selesai') {
            return 'selesai';
        }
        if ($p->tanggal_kembali && Carbon::parse($p->tanggal_kembali)->lt(Carbon::today())) {
            return 'terlambat';
        }
        return 'sedang';
    }

    private function keteranganFilterPeminjam(Request $request): array
    {
        $status = [
            'sedang' => 'Sedang Dipinjam',
            'terlambat' => 'Terlambat Dikembalikan',
            'selesai' => 'Selesai Dipinjam',
        ][$request->input('status')] ?? 'Sedang & Selesai Dipinjam';

        $mulai = $request->input('tanggal_mulai');
        $selesai = $request->input('tanggal_selesai');
        $fmt = fn($t) => Carbon::parse($t)->locale('id')->translatedFormat('d F Y');
        $periode = $mulai && $selesai ? $fmt($mulai) . ' s.d. ' . $fmt($selesai)
            : ($mulai ? 'Sejak ' . $fmt($mulai) : ($selesai ? 'Sampai ' . $fmt($selesai) : 'Semua Periode'));

        $lain = [];
        if ($request->input('jenis') === 'guru') {
            $lain[] = 'Peminjam: Guru';
        } elseif ($request->input('jenis') === 'siswa') {
            $lain[] = 'Peminjam: Siswa';
        }
        if ($request->filled('kelas_id') && ($kelas = Kelas::find($request->input('kelas_id')))) {
            $lain[] = 'Kelas: ' . $kelas->nama_kelas;
        }
        if ($request->filled('search')) {
            $lain[] = 'Pencarian: "' . $request->input('search') . '"';
        }

        return compact('status', 'periode', 'lain');
    }

    private function ringkasanPeminjam($details): array
    {
        $statusList = $details->map(fn($d) => self::statusDetail($d));
        return [
            'peminjam' => $details->pluck('peminjaman.anggota_id')->unique()->count(),
            'judul' => $details->pluck('eksemplar.id_inventori')->unique()->count(),
            'eksemplar' => $details->count(),
            'sedang' => $statusList->filter(fn($s) => $s !== 'selesai')->count(),
            'terlambat' => $statusList->filter(fn($s) => $s === 'terlambat')->count(),
            'selesai' => $statusList->filter(fn($s) => $s === 'selesai')->count(),
        ];
    }

    public function peminjam(Request $request)
    {
        $semua = $this->queryPeminjam($request)->get();
        $ringkasan = $this->ringkasanPeminjam($semua);

        $details = $this->queryPeminjam($request)->paginate(20)->withQueryString();

        return view('admin.laporan.peminjam', [
            'activeMenu' => 'laporanPaket',
            'details' => $details,
            'ringkasan' => $ringkasan,
            'listKelas' => Kelas::orderBy('nama_kelas')->get(),
            'keterangan' => $this->keteranganFilterPeminjam($request),
        ]);
    }

    public function peminjamPdf(Request $request)
    {
        @set_time_limit(300);
        @ini_set('memory_limit', '1024M');

        $details = $this->queryPeminjam($request)->get();
        $grup = $details->groupBy(fn($d) => $d->peminjaman->anggota_id);

        $pdf = Pdf::loadView('admin.laporan.pdf.peminjam', [
            'grup' => $grup,
            'ringkasan' => $this->ringkasanPeminjam($details),
            'keterangan' => $this->keteranganFilterPeminjam($request),
        ])->setPaper('a4', 'portrait');

        $nama = 'laporan-peminjam-buku-' . now()->format('Ymd-His') . '.pdf';

        return $request->boolean('download') ? $pdf->download($nama) : $pdf->stream($nama);
    }
}
