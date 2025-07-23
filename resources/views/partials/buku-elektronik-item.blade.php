<div class="max-w-[160px] w-full rounded-xl overflow-hidden mx-auto">
    <div class="p-3">
        <div class="w-full bg-white rounded-xxl overflow-hidden">
            <div class="flex flex-col items-center">
                <!-- Gambar -->
                <div class="w-full h-48 flex items-center justify-center bg-white mb-1">
                    @if ($buku->cover_image)
                        <img src="{{ asset('buku-cover/' . basename($buku->cover_image)) }}"
                            alt="{{ $buku->judul }}"
                            class="max-h-full max-w-full object-contain shadow-lg" />
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-300 text-sm italic">
                            Tidak ada gambar
                        </div>
                    @endif
                </div>

                <!-- Info -->
                <div class="w-full px-2 text-center">
                    <p class="text-[12px] font-regular text-gray-400 truncate">
                        {{ $buku->penulis ?? '-' }}
                    </p>
                    <h3 class="text-[16px] font-semibold text-text leading-tight mb-2 truncate">
                        {{ $buku->judul }}
                    </h3>
                </div>

                <!-- Tombol Aksi -->
                <div class="w-full px-2 pb-4">
                    <a href="{{ route('detail-buku-elektronik', $buku->id) }}">
                        <button type="button"
                            class="w-full text-white bg-blue-700 hover:bg-blue-800 font-semibold rounded-md text-sm px-5 py-2 transition-all duration-300">
                            Lihat Buku
                        </button>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
