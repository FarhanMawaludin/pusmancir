@extends('layouts.admin-app')

@section('content')
    {{-- Kartu Statistik --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-4 mb-6 w-full">
        {{-- Total Anggota --}}
        <div class="w-full p-4 bg-white border border-gray-200 rounded dark:border-gray-700">
            <div class="flex items-start space-x-4">
                <div class="p-2 rounded-full bg-orange-500 text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M5.121 17.804A4 4 0 0 1 8.6 16h6.8a4 4 0 0 1 3.478 1.804M15 10a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalAnggota }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Anggota</p>
                </div>
            </div>
        </div>

        {{-- Peminjaman Menunggu --}}
        <div class="w-full p-4 bg-white border border-gray-200 rounded dark:border-gray-700">
            <div class="flex items-start space-x-4">
                <div class="p-2 rounded-full bg-orange-500 text-white">
                    <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 3v4a1 1 0 0 1-1 1H5m4 6 2 2 4-4m4-8v16a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1Z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalPeminjamanMenunggu }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Menunggu Peminjaman</p>
                </div>
            </div>
        </div>

        {{-- Total Judul Buku --}}
        <div class="w-full p-4 bg-white border border-gray-200 rounded dark:border-gray-700">
            <div class="flex items-start space-x-4">
                <div class="p-2 rounded-full bg-orange-500 text-white">
                    <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 6c0 1.657-3.134 3-7 3S5 7.657 5 6m14 0c0-1.657-3.134-3-7-3S5 4.343 5 6m14 0v6M5 6v6m0 0c0 1.657 3.134 3 7 3s7-1.343 7-3M5 12v6c0 1.657 3.134 3 7 3s7-1.343 7-3v-6" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalJudulBuku }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Judul Buku</p>
                </div>
            </div>
        </div>

        {{-- Total Eksemplar --}}
        <div class="w-full p-4 bg-white border border-gray-200 rounded dark:border-gray-700">
            <div class="flex items-start space-x-4">
                <div class="p-2 rounded-full bg-orange-500 text-white">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 4h12M6 4v16M6 4H5m13 0v16m0-16h1m-1 16H6m12 0h1M6 20H5M9 7h1v1H9V7Zm5 0h1v1h-1V7Zm-5 4h1v1H9v-1Zm5 0h1v1h-1v-1Zm-3 4h2a1 1 0 0 1 1 1v4h-4v-4a1 1 0 0 1 1-1Z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalEksemplar }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Eksemplar</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Tahun --}}
    <form method="GET" action="{{ route('admin.dashboard.index') }}" class="mb-6 flex flex-wrap gap-4 items-center">
        {{-- Tahun --}}
        <div>
            <label for="year" class="text-sm font-medium text-gray-700">Filter Tahun:</label>
            <select name="year" id="year" onchange="this.form.submit()"
                class="ml-2 border border-gray-300 rounded px-2 py-1.5">
                <option value="all" {{ $selectedYear === 'all' ? 'selected' : '' }}>Semua Tahun</option>
                @foreach ($years as $year)
                    <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Bulan --}}
        @if ($selectedYear !== 'all')
            <div>
                <label for="month" class="text-sm font-medium text-gray-700">Filter Bulan:</label>
                <select name="month" id="month" onchange="this.form.submit()"
                    class="ml-2 border border-gray-300 rounded px-2 py-1">
                    <option value="all" {{ $selectedMonth === 'all' ? 'selected' : '' }}>Semua Bulan</option>
                    @foreach ($months as $num => $name)
                        <option value="{{ $num }}" {{ $selectedMonth == $num ? 'selected' : '' }}>
                            {{ $name }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        {{-- Hari --}}
        @if ($selectedYear !== 'all' && $selectedMonth !== 'all')
            <div>
                <label for="day" class="text-sm font-medium text-gray-700">Filter Hari:</label>
                <select name="day" id="day" onchange="this.form.submit()"
                    class="ml-2 border border-gray-300 rounded px-2 py-1">
                    <option value="all" {{ $selectedDay === 'all' ? 'selected' : '' }}>Semua Hari</option>
                    @foreach ($days as $day)
                        <option value="{{ $day }}" {{ $selectedDay == $day ? 'selected' : '' }}>
                            {{ $day }}</option>
                    @endforeach
                </select>
            </div>
        @endif
    </form>

    {{-- Grafik Peminjaman --}}
    <div class="flex flex-col md:flex-row gap-6 mt-5 mb-5">

        {{-- Grafik Peminjaman --}}
        <div class="bg-white p-4 rounded border border-gray-200 w-full md:w-1/2">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-700">
                    Grafik Peminjaman
                    @if ($selectedYear === 'all')
                        (Semua Tahun)
                    @elseif($selectedMonth === 'all')
                        Tahun {{ $selectedYear }}
                    @elseif($selectedDay === 'all')
                        Tahun {{ $selectedYear }}, Bulan {{ $months[$selectedMonth] ?? $selectedMonth }}
                    @else
                        Tahun {{ $selectedYear }}, Bulan {{ $months[$selectedMonth] ?? $selectedMonth }}, Hari
                        {{ $selectedDay }}
                    @endif
                </h2>
                <button onclick="exportChartToPDF('chartPeminjaman', 'Laporan_Peminjaman')"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Export PDF
                </button>
            </div>
            <canvas id="chartPeminjaman" height="100"></canvas>
        </div>

        {{-- Grafik Pengunjung --}}
        <div class="bg-white p-4 rounded border border-gray-200 w-full md:w-1/2">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-700">
                    Grafik Pengunjung
                    @if ($selectedYear === 'all')
                        (Semua Tahun)
                    @elseif($selectedMonth === 'all')
                        Tahun {{ $selectedYear }}
                    @elseif($selectedDay === 'all')
                        Tahun {{ $selectedYear }}, Bulan {{ $months[$selectedMonth] ?? $selectedMonth }}
                    @else
                        Tahun {{ $selectedYear }}, Bulan {{ $months[$selectedMonth] ?? $selectedMonth }}, Hari
                        {{ $selectedDay }}
                    @endif
                </h2>
                <button onclick="exportChartToPDF('chartPengunjung', 'Laporan_Pengunjung')"
                    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    Export PDF
                </button>
            </div>
            <canvas id="chartPengunjung" height="100"></canvas>
        </div>
    </div>

    <div class="flex flex-col md:flex-row gap-6 mt-5 mb-5">

        {{-- Tabel Top 10 Kunjungan --}}
        <div class="bg-white p-4 rounded border border-gray-200 w-full md:w-1/3 overflow-x-auto">
            <h3 class="font-bold mb-4 text-lg">Top 10 Kunjungan</h3>
            <table class="min-w-full text-sm text-left text-text">
                <thead class="text-xs uppercase bg-gray-100 text-text">
                    <tr>
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3 text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($top10_kunjungan as $index => $item)
                        <tr class="bg-white border-b border-gray-200">
                            <td class="px-4 py-2">{{ $index + 1 }}</td>
                            <td class="px-4 py-2">{{ $item->nama }}</td>
                            <td class="px-4 py-2 text-right">{{ $item->total_kunjungan }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Tabel Top 10 Peminjam --}}
        <div class="bg-white p-4 rounded border border-gray-200 w-full md:w-1/3 overflow-x-auto">
            <h3 class="font-bold mb-4 text-lg">Top 10 Peminjam</h3>
            <table class="min-w-full text-sm text-left text-text">
                <thead class="text-xs uppercase bg-gray-100 text-text">
                    <tr>
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Kelas</th>
                        <th class="px-4 py-3 text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($top10_peminjaman as $index => $item)
                        <tr class="bg-white border-b border-gray-200">
                            <td class="px-4 py-2">{{ $index + 1 }}</td>
                            <td class="px-4 py-2">{{ $item->anggota->user->name ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $item->anggota->kelas->nama_kelas ?? '-' }}</td>
                            <td class="px-4 py-2 text-right">{{ $item->total_peminjaman }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Tabel Top 10 Buku Peminjaman --}}
        <div class="bg-white p-4 rounded border border-gray-200 w-full md:w-1/3 overflow-x-auto">
            <h3 class="font-bold mb-4 text-lg">Top 10 Buku Peminjaman</h3>
            <table class="min-w-full text-sm text-left text-text">
                <thead class="text-xs uppercase bg-gray-100 text-text">
                    <tr>
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Judul Buku</th>
                        <th class="px-4 py-3 text-right">Total Dipinjam</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($top10_buku as $index => $item)
                        <tr class="bg-white border-b border-gray-200">
                            <td class="px-4 py-2">{{ $index + 1 }}</td>
                            <td class="px-4 py-2">{{ $item->judul_buku }}</td>
                            <td class="px-4 py-2 text-right">{{ $item->total_dipinjam }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

    <form method="GET" action="{{ route('admin.dashboard.index') }}" class="flex flex-wrap gap-4 mb-4">
        <div>
            <label for="year" class="text-sm font-medium text-gray-600">Tahun:</label>
            <select name="year" id="year" onchange="this.form.submit()"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <option value="all" {{ $selectedYear == 'all' ? 'selected' : '' }}>Semua</option>
                @foreach ($years as $year)
                    <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                        {{ $year }}</option>
                @endforeach
            </select>
        </div>

        @if ($selectedYear !== 'all')
            <div>
                <label for="month" class="text-sm font-medium text-gray-600">Bulan:</label>
                <select name="month" id="month" onchange="this.form.submit()"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <option value="all" {{ $selectedMonth == 'all' ? 'selected' : '' }}>Semua</option>
                    @foreach ($months as $num => $name)
                        <option value="{{ $num }}" {{ $selectedMonth == $num ? 'selected' : '' }}>
                            {{ $name }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        @if ($selectedYear !== 'all' && $selectedMonth !== 'all')
            <div>
                <label for="day" class="text-sm font-medium text-gray-600">Hari:</label>
                <select name="day" id="day" onchange="this.form.submit()"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <option value="all" {{ $selectedDay == 'all' ? 'selected' : '' }}>Semua</option>
                    @foreach ($days as $day)
                        <option value="{{ $day }}" {{ $selectedDay == $day ? 'selected' : '' }}>
                            {{ $day }}</option>
                    @endforeach
                </select>
            </div>
        @endif
    </form>

    <a href="{{ route('admin.backup.run') }}" class="btn btn-primary">
        Backup Database
    </a>
    

    <div class="bg-white p-4 rounded border border-gray-200 w-full mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-700">
                Grafik Pengunjung Website
            </h2>
            <button onclick="exportChartToPDF('pengunjungWebsite', 'Laporan_Pengunjung')"
                class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm">
                Export PDF
            </button>
        </div>
        <canvas id="pengunjungWebsite" height="100"></canvas>
    </div>

    <script>
        const ctxWebsite = document.getElementById('pengunjungWebsite').getContext('2d');
    
        new Chart(ctxWebsite, {
            type: 'line',
            data: {
                labels: @json($webVisitLabels),
                datasets: [{
                    label: 'Pengunjung Website',
                    data: @json($webVisitData),
                    fill: false,
                    borderColor: '#10B981',
                    backgroundColor: '#10B981',
                    tension: 0.3,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true
                    },
                    tooltip: {
                        callbacks: {
                            label: context => `${context.dataset.label}: ${context.raw}`
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
    
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        // Labels dan data grafik
        const peminjamanLabels = @json($peminjamanLabels);
        const peminjamanData = @json($monthlyPeminjaman);
        const pengunjungLabels = @json($pengunjungLabels);
        const pengunjungData = @json($monthlyPengunjung);

        // Grafik Peminjaman
        const ctxPeminjaman = document.getElementById('chartPeminjaman').getContext('2d');
        new Chart(ctxPeminjaman, {
            type: 'line',
            data: {
                labels: peminjamanLabels,
                datasets: [{
                    label: 'Peminjaman',
                    data: peminjamanData,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.2)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: ctx => `${ctx.parsed.y} peminjaman`
                        }
                    },
                    legend: {
                        display: true
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });

        // Grafik Pengunjung
        const ctxPengunjung = document.getElementById('chartPengunjung').getContext('2d');
        new Chart(ctxPengunjung, {
            type: 'line',
            data: {
                labels: pengunjungLabels,
                datasets: [{
                    label: 'Pengunjung',
                    data: pengunjungData,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.2)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: ctx => `${ctx.parsed.y} pengunjung`
                        }
                    },
                    legend: {
                        display: true
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    </script>

    <script>
        async function exportChartToPDF(chartId, fileName) {
            const {
                jsPDF
            } = window.jspdf;
            const canvasElement = document.getElementById(chartId);

            // Konversi canvas ke gambar
            const canvasImage = await html2canvas(canvasElement, {
                scale: 2
            });

            const imgData = canvasImage.toDataURL('image/png');

            // Buat PDF dan tambahkan gambar chart
            const pdf = new jsPDF({
                orientation: 'landscape',
                unit: 'px',
                format: [canvasImage.width, canvasImage.height]
            });

            pdf.addImage(imgData, 'PNG', 0, 0, canvasImage.width, canvasImage.height);
            pdf.save(`${fileName}.pdf`);
        }
    </script>
@endpush
