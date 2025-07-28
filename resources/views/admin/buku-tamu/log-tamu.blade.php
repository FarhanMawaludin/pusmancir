@extends('layouts.admin-app')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-text">Log Buku Tamu</h1>
    </div>

    {{-- Filter tanggal --}}
    <div class="mb-4">
        <form method="GET" action="{{ route('admin.buku-tamu.log-tamu') }}" class="flex items-center gap-4 flex-wrap">
            <label class="text-text font-medium">Dari:</label>
            <input type="date" name="tanggal_mulai" value="{{ $tanggalMulai }}"
                class="border border-gray-300 rounded px-3 py-2 text-sm text-text">
            <label class="text-text font-medium">Sampai:</label>
            <input type="date" name="tanggal_selesai" value="{{ $tanggalSelesai }}"
                class="border border-gray-300 rounded px-3 py-2 text-sm text-text">

            <select name="kelas" class="border border-gray-300 rounded px-3 py-2 text-sm text-text">
                <option value="">Semua Kelas</option>
                @foreach ($kelasList as $kelas)
                    <option value="{{ $kelas }}" {{ $kelas == $kelasFilter ? 'selected' : '' }}>{{ $kelas }}
                    </option>
                @endforeach
            </select>

            <button type="submit"
                class="bg-blue-700 text-white px-4 py-2 rounded text-sm hover:bg-blue-800">Tampilkan</button>

            <a href="{{ route('admin.buku-tamu.export', request()->all()) }}"
                class="bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700">
                Export Excel
            </a>
        </form>
    </div>

    {{-- Tabel Log Tamu --}}
    <div class="overflow-x-auto relative rounded border border-gray-200">
        <table class="min-w-full text-sm text-left text-text">
            <thead class="text-xs uppercase bg-gray-100 text-text">
                <tr>
                    <th class="px-6 py-3">No</th>
                    <th class="px-6 py-3">Nama</th>
                    <th class="px-6 py-3">NISN</th>
                    <th class="px-6 py-3">Kelas</th>
                    <th class="px-6 py-3">Asal Instansi</th>
                    <th class="px-6 py-3">Keperluan</th>
                    <th class="px-6 py-3">Waktu Kunjungan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bukuTamu as $key => $tamu)
                    <tr class="bg-white border-b border-gray-200">
                        <td class="px-6 py-4">{{ $key + 1 }}</td>
                        <td class="px-6 py-4 font-bold">{{ $tamu->nama }}</td>
                        <td class="px-6 py-4 ">{{ $tamu->nisn }}</td>
                        <td class="px-6 py-4">{{ $tamu->anggota->kelas->nama_kelas ?? '-' }}</th>
                        <td class="px-6 py-4">{{ $tamu->asal_instansi ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $tamu->keperluan ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $tamu->created_at->format('d-m-Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada kunjungan tercatat untuk tanggal ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">
            {{ $bukuTamu->links('pagination::tailwind') }}
        </div>
    </div>
@endsection
