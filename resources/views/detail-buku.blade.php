<div class="max-w-2xl mx-auto mt-10 bg-white p-6 rounded shadow">
    <img src="{{ asset($buku->cover ?? 'img/default-book.png') }}" alt="Sampul"
        class="w-32 h-48 object-contain mb-4 mx-auto">
    <h1 class="text-xl font-bold text-center">{{ $buku->judul_buku }}</h1>
    <p class="text-center text-gray-600 text-sm">Pengarang: {{ $buku->pengarang }}</p>
    <p class="mt-4 text-justify text-sm">{{ $buku->ringkasan_buku ?? 'Deskripsi belum tersedia.' }}</p>
</div>
