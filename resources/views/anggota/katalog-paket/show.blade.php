@extends('layouts.anggota-app')

@section('content')
    <div class="max-w-4xl mx-auto px-6 py-6">
        <!-- Judul Halaman -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-800">Detail Buku</h1>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 gap-10">
            @php
                $available = (int) ($buku->stok_tersedia ?? 0);
                $isAvailable = $available > 0;
                $status = $isAvailable ? 'Tersedia' : 'Tidak Tersedia';
                $statusStyle = $isAvailable ? 'text-green-600' : 'text-red-600';
            @endphp

            <h2 class="text-2xl font-semibold text-text leading-tight text-center mb-1">
                {{ $buku->nama_paket }}
            </h2>

            <!-- Sinopsis -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Deskripsi</h3>
                <p class="text-sm text-gray-700 leading-relaxed text-justify">
                    {{ $buku->deskripsi }}
                </p>
            </div>

            <!-- Tombol Pinjam -->
            <div>
                @if ($buku->stok_tersedia > 0)
                    <form id="pinjamForm" action="{{ route('anggota.peminjaman-paket.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="paket_id" value="{{ $buku->id }}">
                        <button type="button" id="pinjamBtn"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-md font-semibold text-sm transition">
                            Pinjam Buku
                        </button>
                    </form>
                @else
                    <button disabled
                        class="w-full bg-gray-400 text-white py-3 rounded-md font-semibold text-sm cursor-not-allowed">
                        Tidak Tersedia
                    </button>
                @endif
            </div>
        </div>
    </div>
    </div>

    <script>
        document.getElementById('pinjamBtn')?.addEventListener('click', function() {
            const profilLengkap = @json($profilLengkap);

            if (!profilLengkap) {
                Swal.fire({
                    title: 'Profil Belum Lengkap',
                    text: 'Silakan lengkapi profil Anda terlebih dahulu sebelum meminjam buku.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Lengkapi Sekarang',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('anggota.profil.index') }}";
                    }
                });
            } else {
                Swal.fire({
                    title: 'Pinjam Buku Ini?',
                    text: 'Pastikan Anda mengembalikan sebelum tanggal jatuh tempo.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Pinjam',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('pinjamForm').submit();
                    }
                });
            }
        });
    </script>
@endsection
