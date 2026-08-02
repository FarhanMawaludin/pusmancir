@extends('layouts.anggota-app')
@php $activeMenu = 'antrianPaket'; @endphp

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-text">Sistem Peminjaman Buku Paket Pelajaran v.2.0</h2>
</div>

@if($antrianAktif)
<!-- Section 1: Active Queue -->
<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 mb-8 text-center max-w-2xl mx-auto">
    <h3 class="text-lg font-medium text-gray-500 mb-2">Antrian Anda Saat Ini</h3>
    <div class="text-6xl font-bold text-blue-600 my-4">{{ $antrianAktif->nomor_antrian }}</div>
    
    <div class="grid grid-cols-2 gap-4 text-left my-6 border-t border-b py-4">
        <div>
            <span class="block text-sm text-gray-500">Tanggal Kunjungan</span>
            <span class="font-medium text-text">{{ \Carbon\Carbon::parse($antrianAktif->tanggal_kunjungan)->format('d F Y') }}</span>
        </div>
        <div>
            <span class="block text-sm text-gray-500">Status</span>
            <span class="px-3 py-1 text-sm rounded-full bg-orange-100 text-orange-600 font-medium inline-block mt-1">Menunggu</span>
        </div>
    </div>
    
    <!-- NEW: Book Checklist Section -->
    <div class="text-left mt-6 mb-6">
        <h4 class="font-bold text-md mb-4">Pilih Buku Pelajaran yang Ingin Dipinjam</h4>
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
                <label class="flex items-start space-x-3 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                    <input type="checkbox" name="buku_ids[]" value="{{ $buku->id }}" 
                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 mt-1"
                        {{ in_array($buku->id, $selectedBuku) ? 'checked' : '' }}>
                    <span class="text-sm font-medium text-gray-700">{{ $buku->nama_buku }}</span>
                </label>
                @endforeach
            </div>
            <div class="mt-4">
                <button type="button" onclick="confirmPilihBuku()" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 w-full md:w-auto">Simpan Pilihan Buku</button>
            </div>
        </form>
    </div>

    <!-- Info box -->
    <div class="bg-green-50 text-green-700 p-4 rounded-md text-left mb-6 text-sm">
        Datang ke perpustakaan pada tanggal kunjungan dan tunjukkan nomor antrian Anda untuk mengambil buku.
    </div>

    <form id="batal-form" action="{{ route('anggota.antrian-paket.batal', $antrianAktif->id) }}" method="POST">
        @csrf
        <button type="button" onclick="confirmBatal()" class="bg-red-500 text-white px-6 py-2 rounded-md hover:bg-red-600 focus:ring-4 focus:ring-red-300 w-full">Batalkan Antrian</button>
    </form>
</div>

<script>
function confirmBatal() {
    Swal.fire({
        title: 'Batalkan Antrian?',
        text: "Anda yakin ingin membatalkan antrian kunjungan ini?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, batalkan!',
        cancelButtonText: 'Kembali'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('batal-form').submit();
        }
    });
}

function confirmPilihBuku() {
    Swal.fire({
        title: 'Simpan Pilihan Buku?',
        text: 'Pastikan buku yang dipilih sudah sesuai.',
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
                            <span class="px-3 py-1 text-sm rounded-full bg-blue-100 text-blue-800">Dipinjam</span>
                        @elseif($riwayat->status == 'dikembalikan')
                            <span class="px-3 py-1 text-sm rounded-full bg-green-100 text-green-800">Dikembalikan</span>
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
