@extends('layouts.anggota-app')

@section('content')
    <div class="max-w-6xl mx-auto px-6 py-6">
        <!-- Main Content -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <!-- KIRI: Gambar dan info buku -->
            @php
                $available = $buku->inventori?->eksemplar->where('status', '!=', 'dipinjam')->count();

                $status = $available > 0 ? 'Tersedia' : 'dipinjam';
                $statusColor = $available > 0 ? 'text-green-600' : 'text-red-600';
                $style = $status === 'Tersedia' ? 'text-green-700 ' : 'text-red-700 ';
            @endphp
            <div>
                <!-- Gambar -->
                <div class="flex justify-center mb-2">
                    <img src="{{ asset('storage/' . ($buku->cover_buku ?? 'img/book1.png')) }}" alt="Book Cover"
                        class="w-60 h-auto" />
                </div>

                <!-- Judul dan Penulis -->
                <h1 class="text-2xl font-semibold text-text leading-tight text-center mb-1">
                    {{ $buku->judul_buku }}
                </h1>
                <p class="text-md text-center text-gray-500 mb-6">Penulis: {{ $buku->pengarang }}</p>

                <!-- Info Bar -->
                <div class="grid grid-cols-4 gap-4 text-center text-sm text-gray-600 font-medium border-t border-b py-4">
                    <div>
                        <p class="text-gray-400">Kategori</p>
                        <p class="text-text font-semibold">{{ $buku->kategori_buku }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Penerbit</p>
                        <p class="text-text font-semibold">{{ $buku->penerbit }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400">status</p>
                        <p class="font-semibold {{ $style }}">{{ $status }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400">ISBN</p>
                        <p class="text-text font-semibold">
                            {{ $buku->isbn }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- KANAN: Tabs dan Konten -->
            <div class="flex flex-col justify-start h-full">
                <!-- Judul Halaman -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-800 text-left">Detail Buku</h2>
                </div>

                <!-- Tabs -->
                <div class="flex gap-6 border-b mb-4 text-sm font-medium text-gray-500">
                    <button class="pb-2 border-b-2 border-blue-600 text-blue-600">Sinopsis</button>
                </div>

                <!-- Konten Tab Aktif -->
                @php
                    use Carbon\Carbon;
                    // cari satu eksemplar yang belum dipinjam
                    $eksemplarRFID = optional($buku->inventori?->eksemplar->firstWhere('status', '!=', 'dipinjam'))
                        ->no_rfid;

                    $today = Carbon::today()->format('Y-m-d');
                    $returnDate = Carbon::today()->addDays(7)->format('Y-m-d');
                @endphp

                <div class="flex-1">
                    <p class="text-sm text-gray-700 leading-relaxed text-justify mb-8">
                        {{ $buku->ringkasan_buku }}
                    </p>

                    {{-- Form Pinjam --}}
                    @if ($eksemplarRFID)
                        <form id="pinjamForm" action="{{ route('anggota.peminjaman.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="anggota_id" value="{{ auth()->user()->anggota->nisn }}">
                            <input type="hidden" name="eksemplar_id" value="{{ $eksemplarRFID }}">
                            <input type="hidden" name="tanggal_pinjam" value="{{ $today }}">
                            <input type="hidden" name="tanggal_kembali" value="{{ $returnDate }}">

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
                        window.location.href = "{{ route('anggota.profil.index') }}"; // sesuaikan route
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
