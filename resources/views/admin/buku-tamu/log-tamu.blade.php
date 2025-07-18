@extends('layouts.admin-app')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-text">Log Buku Tamu</h1>
    </div>

    {{-- Filter tanggal --}}
    <div class="mb-4">
        <form method="GET" action="{{ route('admin.buku-tamu.log-tamu') }}" class="flex items-center gap-4">
            <label for="tanggal" class="text-text font-medium">Filter Tanggal:</label>
            <input type="date" name="tanggal" id="tanggal" value="{{ $tanggal }}"
                class="border border-gray-300 rounded px-3 py-2 text-sm text-text focus:ring-primary700 focus:border-primary700">
            <button type="submit"
                class="bg-primary700 text-white px-4 py-2 rounded hover:bg-primary800 transition text-sm">
                Tampilkan
            </button>
        </form>
    </div>

    {{-- Tabel Log Tamu --}}
    <div class="overflow-x-auto relative rounded border border-gray-200">
        <table class="min-w-full text-sm text-left text-text">
            <thead class="text-xs uppercase bg-gray-100 text-text">
                <tr>
                    <th class="px-6 py-3">No</th>
                    <th class="px-6 py-3">NISN</th>
                    <th class="px-6 py-3">Nama</th>
                    <th class="px-6 py-3">Asal Instansi</th>
                    <th class="px-6 py-3">Keperluan</th>
                    <th class="px-6 py-3">Waktu Kunjungan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bukuTamu as $key => $tamu)
                    <tr class="bg-white border-b border-gray-200">
                        <td class="px-6 py-4">{{ $key + 1 }}</td>
                        <td class="px-6 py-4 font-bold">{{ $tamu->nisn }}</td>
                        <td class="px-6 py-4">{{ $tamu->nama }}</td>
                        <td class="px-6 py-4">{{ $tamu->asal_instansi ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $tamu->keperluan ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $tamu->created_at->format('d-m-Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada kunjungan tercatat untuk tanggal ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
