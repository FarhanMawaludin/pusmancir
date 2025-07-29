@php
    $available = $buku->inventori?->eksemplar->where('status', '!=', 'dipinjam')->count();
    $status = $available > 0 ? 'Tersedia' : 'Dipinjam';
    $statusColor = $available > 0 ? 'text-green-600' : 'text-red-600';
    $style =
        $status === 'Tersedia'
            ? 'text-green-700 bg-green-100 border border-green-500'
            : 'text-red-700 bg-red-100 border border-red-500';
@endphp

<div class="max-w-[160px] w-full rounded-xl overflow-hidden mx-auto">
    <div class="p-3">
        <div class="w-full bg-white rounded-xxl overflow-hidden">
            <div class="flex flex-col items-center">
                <!-- Gambar -->
                <div class="w-full h-48 flex items-center justify-center bg-white mb-1">
                    <img src="{{ asset($buku->cover_buku ?? 'img/putih.png') }}"
                        alt="{{ $buku->judul_buku }}" class="max-h-full max-w-full object-contain shadow-lg" />
                </div>

                <!-- Info -->
                <div class="w-full px-2">
                    <p class="text-[12px] font-regular text-gray-400 truncate">
                        {{ $buku->pengarang }}
                    </p>
                    <h3 class="text-[16px] font-semibold text-text leading-tight mb-1 truncate">
                        {{ $buku->judul_buku }}
                    </h3>
                    <p class="text-xs font-semibold {{ $statusColor }} mb-3">
                        {{ $status }}
                    </p>
                </div>

                <!-- Tombol -->
                <a href="{{ route('detail-buku', $buku->id) }}" class="w-full px-2 pb-4">
                    <button type="button"
                        class="w-full text-white bg-blue-700 hover:bg-blue-800 font-semibold rounded-md text-sm px-5 py-2 transition-all duration-300">
                        Lihat Buku
                    </button>
                </a>
            </div>
        </div>
    </div>
</div>
