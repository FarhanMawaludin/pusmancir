{{--
    Statistik Peminjaman Buku (Non Paket) — judul & eksemplar, sedang / selesai dipinjam.
    Mengikuti filter Tahun / Bulan di atas.
--}}
@php
    $granularitas = !$statTahunFilter ? 'Tahun' : (!$statBulanFilter ? 'Bulan' : 'Hari');
    $chartLabels = collect($statRekapPeriode)->map(fn($r) => $r['label_chart'] ?? $r['label'])->values();
    $fmt = fn($n) => number_format($n, 0, ',', '.');
@endphp

<style>
    .viz-root {
        --surface-1: #ffffff;
        --text-primary: #0b0b0b;
        --text-secondary: #52514e;
        --text-muted: #898781;
        --grid: #e1e0d9;
        --series-1: #2a78d6;
        --series-2: #eb6834;
        --series-3: #1baf7a;
        --series-4: #eda100;
        --series-5: #e87ba4;
        --series-6: #008300;
        --series-7: #6250d6;
        --status-good: #0ca30c;
        --status-serious: #ec835a;
        --status-critical: #d03b3b;
    }
    .viz-root .stat-tile { background: var(--surface-1); border: 1px solid #e5e7eb; border-radius: 8px; padding: 14px 16px; }
    .viz-root .stat-value { font-size: 1.75rem; line-height: 2rem; font-weight: 700; color: var(--text-primary); font-variant-numeric: tabular-nums; }
    .viz-root .stat-label { font-size: .8rem; color: var(--text-secondary); margin-top: 2px; }
    .viz-root .stat-sub { font-size: .72rem; color: var(--text-muted); margin-top: 6px; }
    .viz-root .swatch { display: inline-block; width: 10px; height: 10px; border-radius: 3px; margin-right: 6px; vertical-align: middle; }
    .viz-root .panel { background: var(--surface-1); border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; }
    .viz-root .panel-title { font-size: 1rem; font-weight: 600; color: #374151; }
    .viz-root .panel-desc { font-size: .75rem; color: var(--text-muted); margin-top: 2px; }
    .viz-root table.rekap td, .viz-root table.rekap th { padding: 6px 10px; font-variant-numeric: tabular-nums; }
    .viz-root .badge-status { display: inline-flex; align-items: center; gap: 4px; padding: 1px 8px; border-radius: 999px; font-size: .7rem; font-weight: 600; }
</style>

<div class="viz-root mb-8">
    <div class="flex flex-wrap items-end justify-between gap-2 mb-3">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Statistik Peminjaman Buku</h2>
            <p class="text-sm text-gray-500">Koleksi non paket &middot; periode: <strong>{{ $statLabelPeriode }}</strong> (mengikuti filter tahun/bulan di atas)</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.laporan.jumlah-buku', array_filter(['tahun' => $statTahunFilter, 'bulan' => $statBulanFilter])) }}"
                class="text-sm px-3 py-2 rounded" style="background-color:#1d4ed8;color:#fff;">Laporan Jumlah Buku</a>
            <a href="{{ route('admin.laporan.peminjam') }}"
                class="text-sm px-3 py-2 rounded" style="background-color:#0f766e;color:#fff;">Laporan Peminjam</a>
        </div>
    </div>

    {{-- Kondisi saat ini --}}
    <p class="text-xs uppercase tracking-wide text-gray-500 mb-2">Kondisi saat ini</p>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-5">
        <div class="stat-tile">
            <div class="stat-value">{{ $fmt($statSnapshot['judul_aktif']) }}</div>
            <div class="stat-label">Judul sedang dipinjam</div>
        </div>
        <div class="stat-tile">
            <div class="stat-value">{{ $fmt($statSnapshot['eks_aktif']) }}</div>
            <div class="stat-label">Eksemplar sedang dipinjam</div>
        </div>
        <div class="stat-tile">
            <div class="stat-value">{{ $fmt($statSnapshot['peminjam_aktif']) }}</div>
            <div class="stat-label">Anggota sedang meminjam</div>
        </div>
        <div class="stat-tile">
            <div class="stat-value" style="color: {{ $statSnapshot['eks_terlambat'] > 0 ? 'var(--status-critical)' : 'var(--text-primary)' }}">
                {{ $fmt($statSnapshot['eks_terlambat']) }}
            </div>
            <div class="stat-label">
                @if ($statSnapshot['eks_terlambat'] > 0)
                    <span aria-hidden="true">&#9888;</span>
                @endif
                Eksemplar terlambat kembali
            </div>
        </div>
        <div class="stat-tile">
            <div class="stat-value">{{ $fmt($statPaketAktif) }}</div>
            <div class="stat-label">Buku paket sedang dipinjam</div>
        </div>
        <div class="stat-tile">
            <div class="stat-value">{{ $fmt($statKomposisiAnggota['guru']) }}</div>
            <div class="stat-label">Anggota guru</div>
            <div class="stat-sub">{{ $fmt($statKomposisiAnggota['siswa']) }} siswa aktif &middot; {{ $fmt($statKomposisiAnggota['alumni']) }} alumni</div>
        </div>
    </div>

    {{-- Periode terpilih --}}
    <p class="text-xs uppercase tracking-wide text-gray-500 mb-2">Periode {{ $statLabelPeriode }}</p>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
        <div class="stat-tile">
            <div class="stat-value">{{ $fmt($statPeriode['judul_total']) }}</div>
            <div class="stat-label">Judul dipinjam</div>
            <div class="stat-sub">{{ $fmt($statPeriode['judul_aktif']) }} masih dipinjam &middot; {{ $fmt($statPeriode['judul_selesai']) }} selesai</div>
        </div>
        <div class="stat-tile">
            <div class="stat-value">{{ $fmt($statPeriode['eks_total']) }}</div>
            <div class="stat-label">Eksemplar dipinjam</div>
            <div class="stat-sub">{{ $fmt($statPeriode['eks_aktif']) }} masih dipinjam &middot; {{ $fmt($statPeriode['eks_selesai']) }} selesai</div>
        </div>
        <div class="stat-tile">
            <div class="stat-value">{{ $fmt($statPeriode['judul_selesai']) }}</div>
            <div class="stat-label">Judul selesai dipinjam</div>
        </div>
        <div class="stat-tile">
            <div class="stat-value">{{ $fmt($statPeriode['eks_selesai']) }}</div>
            <div class="stat-label">Eksemplar selesai dipinjam</div>
        </div>
        <div class="stat-tile">
            <div class="stat-value">{{ $fmt($statPeriode['transaksi']) }}</div>
            <div class="stat-label">Transaksi peminjaman</div>
        </div>
        <div class="stat-tile">
            <div class="stat-value">{{ $fmt($statPeriode['peminjam']) }}</div>
            <div class="stat-label">Peminjam (anggota unik)</div>
            <div class="stat-sub">
                rata-rata {{ $statPeriode['peminjam'] ? number_format($statPeriode['eks_total'] / $statPeriode['peminjam'], 1, ',', '.') : 0 }} eks/peminjam
            </div>
        </div>
    </div>

    {{-- Grafik judul & eksemplar --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
        <div class="panel">
            <div class="panel-title">Judul Buku Dipinjam per {{ $granularitas }}</div>
            <div class="panel-desc">Jumlah judul berbeda menurut tanggal pinjam, dipisah status pengembalian</div>
            <div class="flex gap-4 text-xs text-gray-600 mt-2 mb-1">
                <span><span class="swatch" style="background:var(--series-1)"></span>Sedang dipinjam</span>
                <span><span class="swatch" style="background:var(--series-2)"></span>Selesai dipinjam</span>
            </div>
            <div style="position:relative;height:260px"><canvas id="chartJudulDipinjam" aria-label="Grafik judul buku dipinjam"></canvas></div>
        </div>
        <div class="panel">
            <div class="panel-title">Eksemplar Dipinjam per {{ $granularitas }}</div>
            <div class="panel-desc">Jumlah eksemplar (fisik buku) menurut tanggal pinjam, dipisah status pengembalian</div>
            <div class="flex gap-4 text-xs text-gray-600 mt-2 mb-1">
                <span><span class="swatch" style="background:var(--series-1)"></span>Sedang dipinjam</span>
                <span><span class="swatch" style="background:var(--series-2)"></span>Selesai dipinjam</span>
            </div>
            <div style="position:relative;height:260px"><canvas id="chartEksemplarDipinjam" aria-label="Grafik eksemplar dipinjam"></canvas></div>
        </div>
    </div>

    {{-- Tabel angka per periode --}}
    <div class="panel mb-4">
        <div class="flex flex-wrap justify-between items-center gap-2 mb-3">
            <div>
                <div class="panel-title">Rekap Angka per {{ $granularitas }}</div>
                <div class="panel-desc">Tabel data dari grafik di atas</div>
            </div>
        </div>
        <div class="overflow-x-auto" style="max-height: 360px;">
            <table class="rekap min-w-full text-sm text-left text-gray-700">
                <thead class="text-xs uppercase bg-gray-100 sticky top-0">
                    <tr>
                        <th rowspan="2">{{ $granularitas }}</th>
                        <th colspan="3" class="text-center border-l border-gray-200">Judul</th>
                        <th colspan="3" class="text-center border-l border-gray-200">Eksemplar</th>
                    </tr>
                    <tr>
                        <th class="text-right border-l border-gray-200">Dipinjam</th>
                        <th class="text-right">Sedang</th>
                        <th class="text-right">Selesai</th>
                        <th class="text-right border-l border-gray-200">Dipinjam</th>
                        <th class="text-right">Sedang</th>
                        <th class="text-right">Selesai</th>
                    </tr>
                </thead>
                <tbody>
                    @php $adaBaris = false; @endphp
                    @foreach ($statRekapPeriode as $r)
                        @continue($granularitas === 'Hari' && $r['eks_total'] === 0)
                        @php $adaBaris = true; @endphp
                        <tr class="border-b border-gray-100 {{ $r['eks_total'] === 0 ? 'text-gray-400' : '' }}">
                            <td>{{ $r['label'] }}</td>
                            <td class="text-right border-l border-gray-100 font-semibold">{{ $fmt($r['judul_total']) }}</td>
                            <td class="text-right">{{ $fmt($r['judul_aktif']) }}</td>
                            <td class="text-right">{{ $fmt($r['judul_selesai']) }}</td>
                            <td class="text-right border-l border-gray-100 font-semibold">{{ $fmt($r['eks_total']) }}</td>
                            <td class="text-right">{{ $fmt($r['eks_aktif']) }}</td>
                            <td class="text-right">{{ $fmt($r['eks_selesai']) }}</td>
                        </tr>
                    @endforeach
                    @unless ($adaBaris)
                        <tr><td colspan="7" class="text-center text-gray-400 py-4">Belum ada data peminjaman pada periode ini.</td></tr>
                    @endunless
                </tbody>
                <tfoot class="bg-gray-50 font-bold">
                    <tr>
                        <td>Total</td>
                        <td class="text-right border-l border-gray-200">{{ $fmt($statPeriode['judul_total']) }}</td>
                        <td class="text-right">{{ $fmt($statPeriode['judul_aktif']) }}</td>
                        <td class="text-right">{{ $fmt($statPeriode['judul_selesai']) }}</td>
                        <td class="text-right border-l border-gray-200">{{ $fmt($statPeriode['eks_total']) }}</td>
                        <td class="text-right">{{ $fmt($statPeriode['eks_aktif']) }}</td>
                        <td class="text-right">{{ $fmt($statPeriode['eks_selesai']) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <p class="text-xs text-gray-400 mt-2">* Total judul dihitung sebagai judul berbeda selama periode, sehingga bisa lebih kecil dari penjumlahan per baris.</p>
    </div>

    {{-- Kategori, kelompok peminjam, kondisi koleksi --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
        <div class="panel">
            <div class="panel-title">Eksemplar Dipinjam per Kategori</div>
            <div class="panel-desc">{{ $statLabelPeriode }}</div>
            <div style="position:relative;height:220px" class="mt-2"><canvas id="chartKategori" aria-label="Grafik kategori buku dipinjam"></canvas></div>
            <ul class="mt-3 text-xs space-y-1" id="legendKategori"></ul>
        </div>
        <div class="panel">
            <div class="panel-title">Peminjam Teraktif per Kelas / Guru</div>
            <div class="panel-desc">Eksemplar dipinjam &middot; 10 kelompok teratas</div>
            <div style="position:relative;height:300px" class="mt-2"><canvas id="chartKelompok" aria-label="Grafik peminjaman per kelas"></canvas></div>
        </div>
        <div class="panel">
            <div class="panel-title">Kondisi Eksemplar Koleksi</div>
            <div class="panel-desc">Seluruh koleksi non paket saat ini</div>
            <div style="position:relative;height:220px" class="mt-2"><canvas id="chartStatusEksemplar" aria-label="Grafik kondisi eksemplar"></canvas></div>
            <ul class="mt-3 text-xs space-y-1">
                @php
                    $warnaStatus = ['Tersedia' => 'var(--status-good)', 'Dipinjam' => 'var(--series-1)', 'Rusak' => 'var(--status-serious)', 'Hilang' => 'var(--status-critical)'];
                    $totalEks = array_sum($statStatusEksemplar) ?: 1;
                @endphp
                @foreach ($statStatusEksemplar as $label => $jumlah)
                    <li class="flex justify-between">
                        <span><span class="swatch" style="background:{{ $warnaStatus[$label] }}"></span>{{ $label }}</span>
                        <span class="text-gray-700 font-medium">{{ $fmt($jumlah) }} <span class="text-gray-400">({{ number_format($jumlah / $totalEks * 100, 1, ',', '.') }}%)</span></span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    {{-- Pertumbuhan koleksi & keterlambatan --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="panel">
            <div class="panel-title">Penambahan Koleksi per {{ $statTahunFilter ? 'Bulan' : 'Tahun' }}</div>
            <div class="panel-desc">Judul & eksemplar baru berdasarkan tanggal pembelian/penerimaan</div>
            <div class="flex gap-4 text-xs text-gray-600 mt-2 mb-1">
                <span><span class="swatch" style="background:var(--series-1)"></span>Judul baru</span>
                <span><span class="swatch" style="background:var(--series-2)"></span>Eksemplar baru</span>
            </div>
            <div style="position:relative;height:240px"><canvas id="chartKoleksi" aria-label="Grafik penambahan koleksi"></canvas></div>
        </div>
        <div class="panel overflow-x-auto">
            <div class="flex justify-between items-center">
                <div>
                    <div class="panel-title">Buku Terlambat Dikembalikan</div>
                    <div class="panel-desc">Diurutkan dari yang paling lama melewati batas kembali</div>
                </div>
                <a href="{{ route('admin.pengembalian.index') }}" class="text-xs text-blue-700 hover:underline">Kelola pengembalian &rarr;</a>
            </div>
            <table class="rekap min-w-full text-sm text-left text-gray-700 mt-3">
                <thead class="text-xs uppercase bg-gray-100">
                    <tr>
                        <th>Peminjam</th>
                        <th>Judul Buku</th>
                        <th>Batas Kembali</th>
                        <th class="text-right">Telat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($statBukuTerlambat as $d)
                        @php
                            $batas = \Carbon\Carbon::parse($d->peminjaman->tanggal_kembali);
                            $anggotaTelat = $d->peminjaman->anggota;
                        @endphp
                        <tr class="border-b border-gray-100">
                            <td>
                                <div class="font-medium">{{ $anggotaTelat->user->name ?? '-' }}</div>
                                <div class="text-xs text-gray-400">
                                    {{ $anggotaTelat && $anggotaTelat->isGuru() ? 'Guru' : ($anggotaTelat->kelas->nama_kelas ?? '-') }}
                                </div>
                            </td>
                            <td>{{ \Illuminate\Support\Str::limit($d->eksemplar->inventori->judul_buku ?? '-', 40) }}</td>
                            <td>{{ $batas->format('d-m-Y') }}</td>
                            <td class="text-right">
                                <span class="badge-status" style="background:#fde8e8;color:#9b1c1c;">
                                    <span aria-hidden="true">&#9888;</span>{{ $batas->diffInDays(now()) }} hari
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-gray-400 py-6">Tidak ada buku yang terlambat dikembalikan. &#10003;</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Chart === 'undefined') return;

        const css = getComputedStyle(document.querySelector('.viz-root'));
        const v = name => css.getPropertyValue(name).trim();
        const SERIES = ['--series-1', '--series-2', '--series-3', '--series-4', '--series-5', '--series-6', '--series-7'].map(v);
        const OTHER = '#9ca3af';
        const fmt = n => new Intl.NumberFormat('id-ID').format(n);

        Chart.defaults.font.family = getComputedStyle(document.body).fontFamily;
        Chart.defaults.color = v('--text-muted');

        const axisOpts = {
            x: { grid: { display: false }, border: { color: '#c3c2b7' }, ticks: { color: v('--text-muted') } },
            y: { beginAtZero: true, grid: { color: v('--grid') }, border: { display: false },
                 ticks: { precision: 0, color: v('--text-muted'), callback: val => fmt(val) } }
        };
        const tooltip = {
            mode: 'index', intersect: false,
            callbacks: { label: c => ` ${c.dataset.label}: ${fmt(c.parsed.y ?? c.parsed)}` }
        };
        const barStyle = color => ({
            backgroundColor: color, borderColor: v('--surface-1'), borderWidth: { top: 0, left: 1, right: 1, bottom: 0 },
            borderRadius: { topLeft: 4, topRight: 4 }, borderSkipped: 'bottom',
            maxBarThickness: 28, categoryPercentage: 0.7, barPercentage: 0.95,
        });

        const labels = @json($chartLabels);
        const rekap = @json($statRekapPeriode);

        function groupedBar(id, keyAktif, keySelesai) {
            new Chart(document.getElementById(id), {
                type: 'bar',
                data: {
                    labels,
                    datasets: [
                        { label: 'Sedang dipinjam', data: rekap.map(r => r[keyAktif]), ...barStyle(SERIES[0]) },
                        { label: 'Selesai dipinjam', data: rekap.map(r => r[keySelesai]), ...barStyle(SERIES[1]) },
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: { legend: { display: false }, tooltip },
                    scales: axisOpts,
                }
            });
        }
        groupedBar('chartJudulDipinjam', 'judul_aktif', 'judul_selesai');
        groupedBar('chartEksemplarDipinjam', 'eks_aktif', 'eks_selesai');

        // Kategori (maks 7 warna, sisanya dilipat ke "Lainnya")
        const kategori = @json($statPerKategori);
        const top = kategori.slice(0, 7);
        const sisa = kategori.slice(7).reduce((a, k) => a + k.eksemplar, 0);
        const katLabels = top.map(k => k.kategori).concat(sisa ? ['Lainnya'] : []);
        const katData = top.map(k => k.eksemplar).concat(sisa ? [sisa] : []);
        const katColors = top.map((_, i) => SERIES[i]).concat(sisa ? [OTHER] : []);
        const legendKat = document.getElementById('legendKategori');
        if (katData.length) {
            new Chart(document.getElementById('chartKategori'), {
                type: 'doughnut',
                data: { labels: katLabels, datasets: [{ data: katData, backgroundColor: katColors, borderColor: v('--surface-1'), borderWidth: 2 }] },
                options: { responsive: true, maintainAspectRatio: false, cutout: '62%',
                    plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => ` ${c.label}: ${fmt(c.parsed)} eks` } } } }
            });
            const total = katData.reduce((a, b) => a + b, 0) || 1;
            legendKat.innerHTML = katLabels.map((l, i) =>
                `<li class="flex justify-between"><span><span class="swatch" style="background:${katColors[i]}"></span>${l}</span>` +
                `<span class="text-gray-700 font-medium">${fmt(katData[i])} <span class="text-gray-400">(${(katData[i] / total * 100).toFixed(1).replace('.', ',')}%)</span></span></li>`
            ).join('');
        } else {
            legendKat.innerHTML = '<li class="text-gray-400 text-center">Belum ada data.</li>';
        }

        // Per kelas / guru (horizontal bar, satu seri)
        const kelompok = @json($statPerKelompok);
        new Chart(document.getElementById('chartKelompok'), {
            type: 'bar',
            data: {
                labels: kelompok.map(k => k.kelompok),
                datasets: [{
                    label: 'Eksemplar dipinjam', data: kelompok.map(k => k.eksemplar),
                    backgroundColor: SERIES[0], borderRadius: { topRight: 4, bottomRight: 4 }, borderSkipped: 'left', maxBarThickness: 18,
                }]
            },
            options: {
                indexAxis: 'y', responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { callbacks: {
                    label: c => ` ${fmt(c.parsed.x)} eksemplar`,
                    afterLabel: c => ` ${fmt(kelompok[c.dataIndex].judul)} judul · ${fmt(kelompok[c.dataIndex].peminjam)} peminjam`
                } } },
                scales: {
                    x: { beginAtZero: true, grid: { color: v('--grid') }, border: { display: false }, ticks: { precision: 0 } },
                    y: { grid: { display: false }, ticks: { color: v('--text-secondary') } }
                }
            }
        });

        // Kondisi eksemplar (warna status + label di legenda)
        const statusEks = @json($statStatusEksemplar);
        new Chart(document.getElementById('chartStatusEksemplar'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(statusEks),
                datasets: [{ data: Object.values(statusEks),
                    backgroundColor: [v('--status-good'), v('--series-1'), v('--status-serious'), v('--status-critical')],
                    borderColor: v('--surface-1'), borderWidth: 2 }]
            },
            options: { responsive: true, maintainAspectRatio: false, cutout: '62%',
                plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => ` ${c.label}: ${fmt(c.parsed)} eks` } } } }
        });

        // Penambahan koleksi
        const koleksi = @json($statPertumbuhanKoleksi);
        new Chart(document.getElementById('chartKoleksi'), {
            type: 'line',
            data: {
                labels: koleksi.labels,
                datasets: [
                    { label: 'Judul baru', data: koleksi.judul, borderColor: SERIES[0], backgroundColor: SERIES[0], borderWidth: 2, pointRadius: 4, pointHoverRadius: 6, tension: 0.25 },
                    { label: 'Eksemplar baru', data: koleksi.eksemplar, borderColor: SERIES[1], backgroundColor: SERIES[1], borderWidth: 2, pointRadius: 4, pointHoverRadius: 6, tension: 0.25 },
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: { legend: { display: false }, tooltip },
                scales: axisOpts,
            }
        });
    });
</script>
