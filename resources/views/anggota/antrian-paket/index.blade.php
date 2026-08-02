@extends('layouts.anggota-app')
@php $activeMenu = 'antrianPaket'; @endphp

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-text">Sistem Peminjaman Buku Paket Pelajaran v.2.0</h2>
</div>

<!-- Card Panduan & Prosedur Peminjaman -->
<div class="bg-white rounded-lg shadow-sm border border-blue-100 p-6 mb-6">
    <div class="flex items-center space-x-3 mb-4 pb-3 border-b border-gray-100">
        <div class="p-2 bg-blue-100 text-blue-600 rounded-lg">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <div>
            <h3 class="font-bold text-lg text-gray-800">Panduan & Prosedur Peminjaman Buku Paket</h3>
            <p class="text-xs text-gray-500">Harap perhatikan langkah-langkah peminjaman buku paket di bawah ini:</p>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- Langkah 1 -->
        <div class="p-4 rounded-xl bg-blue-50/60 border border-blue-100 hover:shadow-sm transition flex flex-col justify-between">
            <div>
                <div class="flex items-center space-x-3 mb-2">
                    <span class="w-7 h-7 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center shadow-sm shrink-0">1</span>
                    <h4 class="font-bold text-sm text-blue-950">Booking Tanggal</h4>
                </div>
                <p class="text-xs text-gray-600 leading-relaxed">
                    Lakukan booking peminjaman di tanggal yang masih tersedia kuotanya. Nomor antrian <strong>hanya berlaku di tanggal booking tersebut</strong>.
                </p>
            </div>
        </div>

        <!-- Langkah 2 -->
        <div class="p-4 rounded-xl bg-blue-50/60 border border-blue-100 hover:shadow-sm transition flex flex-col justify-between">
            <div>
                <div class="flex items-center space-x-3 mb-2">
                    <span class="w-7 h-7 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center shadow-sm shrink-0">2</span>
                    <h4 class="font-bold text-sm text-blue-950">Datang Sesuai Tanggal</h4>
                </div>
                <p class="text-xs text-gray-600 leading-relaxed">
                    Hadir ke perpustakaan pada tanggal yang dibooking. <em>Jika tidak hadir, antrian hangus & Anda bisa booking di tanggal berikutnya.</em>
                </p>
            </div>
        </div>

        <!-- Langkah 3 -->
        <div class="p-4 rounded-xl bg-blue-50/60 border border-blue-100 hover:shadow-sm transition flex flex-col justify-between">
            <div>
                <div class="flex items-center space-x-3 mb-2">
                    <span class="w-7 h-7 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center shadow-sm shrink-0">3</span>
                    <h4 class="font-bold text-sm text-blue-950">Login HP & Tunjukkan Nomor</h4>
                </div>
                <p class="text-xs text-gray-600 leading-relaxed">
                    Disarankan datang dalam kondisi <strong>sudah login di HP masing-masing</strong> dan tunjukkan nomor booking ke petugas perpustakaan.
                </p>
            </div>
        </div>

        <!-- Langkah 4 -->
        <div class="p-4 rounded-xl bg-blue-50/60 border border-blue-100 hover:shadow-sm transition flex flex-col justify-between">
            <div>
                <div class="flex items-center space-x-3 mb-2">
                    <span class="w-7 h-7 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center shadow-sm shrink-0">4</span>
                    <h4 class="font-bold text-sm text-blue-950">Pilih Buku Paket</h4>
                </div>
                <p class="text-xs text-gray-600 leading-relaxed">
                    Siswa bebas memilih dan mengambil sendiri buku paket pelajaran yang diinginkan di perpustakaan.
                </p>
            </div>
        </div>

        <!-- Langkah 5 -->
        <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 hover:shadow-sm transition flex flex-col justify-between md:col-span-2 lg:col-span-1">
            <div>
                <div class="flex items-center space-x-3 mb-2">
                    <span class="w-7 h-7 rounded-full bg-amber-600 text-white text-xs font-bold flex items-center justify-center shadow-sm shrink-0">5</span>
                    <h4 class="font-bold text-sm text-amber-900">Ceklist Mandiri (Penting!)</h4>
                </div>
                <p class="text-xs text-amber-900 leading-relaxed">
                    <strong>SISWA MELAKUKAN CEKLIST MANDIRI</strong> buku yang dipinjam pada sistem.
                    <span class="block mt-1 text-[11px] text-amber-800 italic">
                        *Ceklist ini menjadi dasar rekap data petugas. Jika Anda salah ceklist dikhawatirkan Anda bertanggung jawab terhadap buku yang tidak Anda pinjam. Setiap buku yang diceklist siswa bertanggung jawab penuh.
                    </span>
                </p>
            </div>
        </div>

        <!-- Langkah 6 -->
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 hover:shadow-sm transition flex flex-col justify-between md:col-span-3 lg:col-span-1">
            <div>
                <div class="flex items-center space-x-3 mb-2">
                    <span class="w-7 h-7 rounded-full bg-emerald-600 text-white text-xs font-bold flex items-center justify-center shadow-sm shrink-0" style="background-color: #059669; color: #ffffff;">6</span>
                    <h4 class="font-bold text-sm text-emerald-950">Selesai & Bawa Tas</h4>
                </div>
                <p class="text-xs text-emerald-900 leading-relaxed">
                    Siswa telah selesai melakukan proses peminjaman buku paket pelajaran.
                    <span class="block mt-1 font-semibold text-emerald-800">
                        💡 <em>Disarankan membawa goodie bag / tas besar dari rumah untuk membawa buku paket karena berat.</em>
                    </span>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Banner Informasi Ketentuan Antrian -->
<div class="bg-blue-50 border-l-4 border-blue-600 p-4 rounded-r-lg mb-8">
    <div class="flex items-start">
        <svg class="w-5 h-5 text-blue-600 mr-3 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <div class="text-xs text-blue-900 leading-relaxed">
            <strong class="text-sm font-bold block mb-1">📌 Ketentuan Penting Booking Antrian:</strong>
            <ul class="list-disc list-inside space-y-1">
                <li>Nomor antrian <strong>hanya berlaku pada tanggal kunjungan</strong> yang Anda pilih saat booking.</li>
                <li>Jika Anda <strong>tidak hadir/tidak mengambil buku pada tanggal booking</strong>, nomor antrian tersebut otomatis <strong>HANGUS</strong>.</li>
                <li>Setelah tanggal booking lewat (hangus), Anda secara otomatis dapat melakukan <strong>booking ulang untuk tanggal berikutnya</strong> yang masih tersedia kuotanya.</li>
            </ul>
        </div>
    </div>
</div>

@if($antrianAktif)
<!-- Section 1: Active Queue -->
<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 mb-8 text-center max-w-2xl mx-auto">
    <h3 class="text-lg font-medium text-gray-500 mb-2">Antrian Anda Saat Ini</h3>
    <div class="text-6xl font-bold text-blue-600 my-4">{{ $antrianAktif->nomor_antrian }}</div>
    
    <div class="grid grid-cols-2 gap-4 text-left my-6 border-t border-b py-4">
        <div>
            <span class="block text-sm text-gray-500">Tanggal Kunjungan</span>
            <span class="font-medium text-text">{{ \Carbon\Carbon::parse($antrianAktif->tanggal_kunjungan)->translatedFormat('d F Y') }}</span>
        </div>
        <div>
            <span class="block text-sm text-gray-500">Status</span>
            <span class="px-3 py-1 text-sm rounded-full bg-orange-100 text-orange-600 font-medium inline-block mt-1">Menunggu</span>
        </div>
    </div>
    
    <!-- NEW: Book Checklist Section -->
    <div class="text-left mt-6 mb-6">
        <div class="flex items-center justify-between mb-3">
            <h4 class="font-bold text-md text-gray-800">Ceklist Mandiri Buku yang Dipinjam</h4>
            <span class="text-xs text-amber-700 bg-amber-100 font-semibold px-2 py-0.5 rounded">Wajib Diisi Siswa</span>
        </div>
        <p class="text-xs text-gray-500 mb-4">Silahkan centang buku paket yang Anda ambil di perpustakaan sebagai dasar rekap oleh petugas.</p>
        <form id="pilih-buku-form" action="{{ route('anggota.antrian-paket.pilih-buku', $antrianAktif->id) }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @php
                    $selectedBuku = [];
                    if ($peminjamanAktif && $peminjamanAktif->detailPeminjamanBukuPaket) {
                        $selectedBuku = $peminjamanAktif->detailPeminjamanBukuPaket->pluck('buku_paket_mapel_id')->toArray();
                    }
                @endphp
                @foreach($bukuMapel as $buku)
                <label class="flex items-start space-x-3 p-3 border rounded-lg hover:bg-blue-50/50 transition cursor-pointer border-gray-200">
                    <input type="checkbox" name="buku_ids[]" value="{{ $buku->id }}" 
                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 mt-1"
                        {{ in_array($buku->id, $selectedBuku) ? 'checked' : '' }}>
                    <span class="text-sm font-medium text-gray-700">{{ $buku->nama_buku }}</span>
                </label>
                @endforeach
            </div>
            <div class="mt-4">
                <button type="button" onclick="confirmPilihBuku()" class="bg-blue-600 text-white px-6 py-2.5 rounded-md hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 w-full md:w-auto font-medium text-sm transition">Simpan Pilihan Buku</button>
            </div>
        </form>
    </div>

    <!-- Info box -->
    <div class="bg-amber-50 border border-amber-200 text-amber-900 p-4 rounded-md text-left mb-2 text-xs leading-relaxed">
        <div class="font-bold text-sm mb-1 text-amber-900 flex items-center gap-1.5">
            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Petunjuk & Ketentuan Antrian:
        </div>
        <ul class="list-disc list-inside space-y-1.5 pl-1">
            <li>Tunjukkan nomor antrian di atas kepada petugas perpustakaan pada tanggal <strong>{{ \Carbon\Carbon::parse($antrianAktif->tanggal_kunjungan)->translatedFormat('d F Y') }}</strong>.</li>
            <li><strong>Masa Berlaku:</strong> Nomor antrian ini <strong>hanya berlaku di tanggal booking</strong>. Jika Anda tidak hadir pada tanggal tersebut, antrian otomatis <strong>hangus</strong> dan Anda dapat booking ulang untuk jadwal berikutnya.</li>
            <li>Pastikan Anda melakukan <strong>CEKLIST MANDIRI</strong> untuk setiap buku yang diambil. Siswa bertanggung jawab penuh atas buku yang diceklist.</li>
            <li>Disarankan membawa <strong>goodie bag / tas besar</strong> untuk kemudahan membawa buku paket.</li>
        </ul>
    </div>
</div>

<script>
function confirmPilihBuku() {
    Swal.fire({
        title: 'Simpan Pilihan Buku?',
        text: 'Pastikan buku yang diceklist sudah sesuai dengan fisik buku yang Anda ambil.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#1d4ed8',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Simpan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('pilih-buku-form').submit();
        }
    });
}
</script>

@else
<!-- Section 2: Book Now -->
<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 mb-8">
    <h3 class="font-bold text-lg border-b pb-2 mb-4">Pilih Tanggal Kunjungan</h3>
    
    @if(count($availableDates) > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach($availableDates as $date)
        <div class="border rounded-lg p-4 text-center hover:shadow-md transition">
            <div class="font-bold text-lg mb-1">{{ \Carbon\Carbon::parse($date->tanggal)->format('d M Y') }}</div>
            <div class="text-sm text-gray-500 mb-4">Sisa Kuota: <span class="font-bold text-blue-600">{{ $date->kuota - $date->terisi }}</span></div>
            
            <form action="{{ route('anggota.antrian-paket.store') }}" method="POST" id="booking-form-{{ $date->id }}">
                @csrf
                <input type="hidden" name="tanggal_kunjungan" value="{{ $date->tanggal }}">
                <button type="button" onclick="confirmBooking({{ $date->id }}, '{{ \Carbon\Carbon::parse($date->tanggal)->format('d-m-Y') }}')" class="bg-blue-700 text-white px-4 py-2 rounded-md hover:bg-blue-800 w-full text-sm">Booking</button>
            </form>
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-yellow-50 text-yellow-700 p-4 rounded-md">
        Tidak ada jadwal antrian tersedia saat ini.
    </div>
    @endif
</div>

<script>
function confirmBooking(id, dateStr) {
    Swal.fire({
        title: 'Konfirmasi Booking',
        text: `Anda akan mengambil antrian untuk tanggal ${dateStr}. Lanjutkan?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#1d4ed8',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Booking!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('booking-form-' + id).submit();
        }
    });
}
</script>
@endif

<!-- Section 3: Riwayat Peminjaman -->
<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b">
        <h3 class="font-bold text-lg">Riwayat Peminjaman</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left text-text">
            <thead class="text-xs uppercase bg-gray-100 text-text">
                <tr>
                    <th class="px-6 py-4">Tanggal</th>
                    <th class="px-6 py-4">No Antrian</th>
                    <th class="px-6 py-4">Buku Dipinjam</th>
                    <th class="px-6 py-4">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayatPeminjaman as $riwayat)
                <tr class="border-b">
                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($riwayat->tanggal_pinjam)->format('d-m-Y') }}</td>
                    <td class="px-6 py-4 font-medium">{{ $riwayat->antrianPaket->nomor_antrian ?? '-' }}</td>
                    <td class="px-6 py-4">
                        {{ $riwayat->detailPeminjamanBukuPaket ? $riwayat->detailPeminjamanBukuPaket->pluck('bukuPaketMapel.nama_buku')->join(', ') : '-' }}
                    </td>
                    <td class="px-6 py-4">
                        @if($riwayat->status == 'dipinjam')
                            <span class="px-3 py-1 text-sm rounded-full bg-blue-100 text-blue-800 font-medium">Dipinjam</span>
                        @elseif($riwayat->status == 'dikembalikan')
                            <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-800 font-semibold inline-block">
                                Dikembalikan @if($riwayat->tanggal_kembali) pada {{ \Carbon\Carbon::parse($riwayat->tanggal_kembali)->format('d-m-Y') }} @endif
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center">Belum ada riwayat peminjaman.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(isset($riwayatPeminjaman) && method_exists($riwayatPeminjaman, 'hasPages') && $riwayatPeminjaman->hasPages())
    <div class="px-6 py-4">
        {{ $riwayatPeminjaman->links('pagination::tailwind') }}
    </div>
    @endif
</div>
@endsection

