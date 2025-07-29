@extends('layouts.admin-app')

@section('content')
    {{-- Kartu Statistik --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-4 mb-10 w-full">
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
    <form method="GET" action="{{ route('admin.dashboard.index') }}" class="mb-6">
        <label for="year" class="text-sm font-medium text-gray-700">Filter Tahun:</label>
        <select name="year" id="year" onchange="this.form.submit()"
            class="ml-2 border border-gray-300 rounded px-2 py-1">
            <option value="all" {{ $selectedYear === 'all' ? 'selected' : '' }}>Semua Tahun</option>
            @foreach ($years as $year)
                <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}
                </option>
            @endforeach
        </select>
    </form>

    {{-- Grafik Peminjaman --}}
    <div class="bg-white p-4 rounded border border-gray-200 mb-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-2">
            Grafik Peminjaman {{ $selectedYear === 'all' ? '(Semua Tahun)' : 'Tahun ' . $selectedYear }}
        </h2>
        <canvas id="chartPeminjaman" height="100"></canvas>
    </div>

    {{-- Grafik Pengunjung --}}
    <div class="bg-white p-4 rounded border border-gray-200">
        <h2 class="text-lg font-semibold text-gray-700 mb-2">
            Grafik Pengunjung {{ $selectedYear === 'all' ? '(Semua Tahun)' : 'Tahun ' . $selectedYear }}
        </h2>
        <canvas id="chartPengunjung" height="100"></canvas>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Jika tahun "all", gunakan label tahun, kalau tahun tertentu gunakan label bulan
        const selectedYear = @json($selectedYear);

        // Label untuk tahun semua dan bulan
        const labelsTahun = @json($peminjamanLabels); // misal ['2021', '2022', '2023'] ketika all
        const labelsBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        // Label yang akan dipakai tergantung selectedYear
        const labels = selectedYear === 'all' ? labelsTahun : labelsBulan;

        // Data peminjaman
        const dataPeminjaman = @json($monthlyPeminjaman);

        // Grafik Peminjaman
        const ctxPeminjaman = document.getElementById('chartPeminjaman').getContext('2d');
        new Chart(ctxPeminjaman, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Peminjaman',
                    data: dataPeminjaman,
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

        // Data pengunjung
        const dataPengunjung = @json($monthlyPengunjung);

        // Grafik Pengunjung
        const ctxPengunjung = document.getElementById('chartPengunjung').getContext('2d');
        new Chart(ctxPengunjung, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pengunjung',
                    data: dataPengunjung,
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
@endpush
