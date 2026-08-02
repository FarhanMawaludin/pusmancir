@extends('layouts.admin-app')
@php $activeMenu = 'antrianPaket'; @endphp

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h2 class="text-2xl font-bold text-text">Rekap Data Peminjaman Buku Paket</h2>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <span class="text-sm font-medium text-gray-500">Total Peminjam</span>
        <span class="text-2xl font-bold text-blue-600 block mt-1">{{ $totalPeminjam }}</span>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <span class="text-sm font-medium text-gray-500">Total Buku Dipinjam</span>
        <span class="text-2xl font-bold text-green-600 block mt-1">{{ $totalBukuDipinjam }}</span>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <span class="text-sm font-medium text-gray-500">Buku Terpopuler</span>
        <span class="text-2xl font-bold text-purple-600 block mt-1">{{ $bukuPopuler->first()->bukuPaketMapel->nama_buku ?? '-' }}</span>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 mb-6">
    <form action="{{ route('admin.antrian-paket.riwayat') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4 items-end">
        <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Cari (Nama/NISN)</label>
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Nama / NISN..." class="block w-full rounded-md bg-white px-3 py-2 text-sm text-text border border-gray-300 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-600">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Filter Kelas</label>
            <select name="kelas" class="block w-full rounded-md bg-white px-3 py-2 text-sm text-text border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600">
                <option value="">-- Semua Kelas --</option>
                @foreach($kelasList as $k)
                    <option value="{{ $k->id }}" {{ ($kelas == $k->id || $kelas == $k->nama_kelas) ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Tampilkan</label>
            <select name="per_page" class="block w-full rounded-md bg-white px-3 py-2 text-sm text-text border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600">
                <option value="10" {{ ($perPage == 10) ? 'selected' : '' }}>10 Data</option>
                <option value="50" {{ ($perPage == 50) ? 'selected' : '' }}>50 Data</option>
                <option value="100" {{ ($perPage == 100) ? 'selected' : '' }}>100 Data</option>
                <option value="semua" {{ ($perPage == 'semua' || $perPage == 'all') ? 'selected' : '' }}>Semua Data</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Dari Tanggal</label>
            <input type="date" name="tanggal_mulai" value="{{ $tanggalMulai ?? '' }}" class="block w-full rounded-md bg-white px-3 py-2 text-sm text-text border border-gray-300 focus:ring-2 focus:ring-blue-600">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Sampai Tanggal</label>
            <input type="date" name="tanggal_selesai" value="{{ $tanggalSelesai ?? '' }}" class="block w-full rounded-md bg-white px-3 py-2 text-sm text-text border border-gray-300 focus:ring-2 focus:ring-blue-600">
        </div>
        <div class="flex space-x-2">
            <button type="submit" class="bg-blue-700 text-white px-4 py-2 rounded-md hover:bg-blue-800 text-sm font-medium w-full transition">Filter</button>
            <a href="{{ route('admin.antrian-paket.riwayat') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 text-center text-sm font-medium w-full transition">Reset</a>
        </div>
    </form>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left text-text">
            <thead class="text-xs uppercase bg-gray-100 text-text">
                <tr>
                    <th class="px-6 py-4">No</th>
                    <th class="px-6 py-4">Nama</th>
                    <th class="px-6 py-4">NISN</th>
                    <th class="px-6 py-4">Kelas</th>
                    <th class="px-6 py-4">Buku Dipinjam</th>
                    <th class="px-6 py-4 text-center">Jumlah</th>
                    <th class="px-6 py-4">Tgl Pinjam</th>
                    <th class="px-6 py-4">Tgl Kembali</th>
                    <th class="px-6 py-4 text-center">Status / Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayat as $key => $item)
                <tr class="border-b hover:bg-gray-50/50 transition">
                    <td class="px-6 py-4 font-medium">{{ $riwayat->firstItem() + $key }}</td>
                    <td class="px-6 py-4 font-bold text-gray-800">{{ $item->anggota->user->name ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $item->anggota->nisn ?? '-' }}</td>
                    <td class="px-6 py-4 font-semibold text-blue-700">{{ $item->anggota->kelas->nama_kelas ?? '-' }}</td>
                    <td class="px-6 py-4 max-w-xs">
                        <div class="flex items-center justify-between gap-2">
                            <span>{{ $item->detailPeminjamanBukuPaket->pluck('bukuPaketMapel.nama_buku')->join(', ') ?: '-' }}</span>
                            <button type="button" onclick="openEditBukuModal({{ $item->id }})" class="text-blue-600 hover:text-blue-800 text-xs font-semibold underline shrink-0">
                                Edit Buku
                            </button>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center font-bold">
                        {{ $item->detailPeminjamanBukuPaket->count() }}
                    </td>
                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d-m-Y') }}</td>
                    <td class="px-6 py-4">
                        @if($item->tanggal_kembali)
                            <span class="text-green-700 font-semibold">{{ \Carbon\Carbon::parse($item->tanggal_kembali)->format('d-m-Y') }}</span>
                        @else
                            <span class="text-gray-400 italic">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($item->status == 'dipinjam')
                            <div class="flex items-center justify-center space-x-2">
                                <span class="px-2.5 py-1 text-xs rounded-full bg-blue-100 text-blue-800 font-medium">Dipinjam</span>
                                <form id="form-kembali-{{ $item->id }}" action="{{ route('admin.antrian-paket.kembali', $item->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="button" onclick="confirmKembali({{ $item->id }}, '{{ addslashes($item->anggota->user->name ?? '') }}')" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1 rounded text-xs font-semibold shadow-sm transition inline-flex items-center gap-1" style="background-color: #059669; color: #ffffff;">
                                        Kembali
                                    </button>
                                </form>
                            </div>
                        @elseif($item->status == 'dikembalikan')
                            <div class="text-center">
                                <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-800 font-semibold inline-block">
                                    Dikembalikan
                                </span>
                                @if($item->tanggal_kembali)
                                    <div class="text-[11px] text-green-700 mt-1">pada {{ \Carbon\Carbon::parse($item->tanggal_kembali)->format('d-m-Y') }}</div>
                                @endif
                            </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-4 text-center text-gray-500">Tidak ada riwayat peminjaman.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($riwayat->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $riwayat->links('pagination::tailwind') }}
    </div>
    @endif
</div>

<!-- Modal Edit Pilihan Buku Paket oleh Admin -->
<div id="modal-edit-buku" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full p-6 relative">
        <button type="button" onclick="closeEditBukuModal()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
        <h3 class="text-lg font-bold text-gray-800 mb-1">Edit Pilihan Buku Paket</h3>
        <p class="text-xs text-gray-500 mb-4" id="modal-siswa-info">Siswa: -</p>
        
        <form id="form-edit-buku" method="POST" action="">
            @csrf
            <div class="space-y-2 max-h-60 overflow-y-auto mb-4 pr-1" id="modal-buku-container">
                <!-- Checkboxes populated dynamically by JS -->
            </div>
            
            <div class="flex justify-end space-x-2 border-t pt-4">
                <button type="button" onclick="closeEditBukuModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium rounded-md">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white text-sm font-medium rounded-md">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
const allBukuMapelData = @json($allBukuMapel);
const riwayatData = @json($riwayat->items());

function openEditBukuModal(peminjamanId) {
    const item = riwayatData.find(r => r.id === peminjamanId);
    if (!item) return;
    
    document.getElementById('modal-siswa-info').innerText = `Siswa: ${item.anggota?.user?.name || '-'} (${item.anggota?.kelas?.nama_kelas || '-'})`;
    
    const form = document.getElementById('form-edit-buku');
    form.action = `/admin/antrian-paket/${peminjamanId}/update-buku`;
    
    const selectedBukuIds = item.detail_peminjaman_buku_paket ? item.detail_peminjaman_buku_paket.map(d => d.buku_paket_mapel_id) : [];
    
    const namaKelas = item.anggota?.kelas?.nama_kelas || '';
    let tingkatKelas = 'X';
    if (/XII/i.test(namaKelas)) {
        tingkatKelas = /IPA/i.test(namaKelas) ? 'XII IPA' : (/IPS/i.test(namaKelas) ? 'XII IPS' : 'XII');
    } else if (/XI/i.test(namaKelas)) {
        tingkatKelas = /IPA/i.test(namaKelas) ? 'XI IPA' : (/IPS/i.test(namaKelas) ? 'XI IPS' : 'XI');
    }
    
    const container = document.getElementById('modal-buku-container');
    container.innerHTML = '';
    
    const filteredBuku = allBukuMapelData.filter(b => b.tingkat_kelas === tingkatKelas || b.tingkat_kelas === 'Semua' || !b.tingkat_kelas);
    const booksToRender = filteredBuku.length > 0 ? filteredBuku : allBukuMapelData;
    
    booksToRender.forEach(buku => {
        const isChecked = selectedBukuIds.includes(buku.id) ? 'checked' : '';
        const label = document.createElement('label');
        label.className = 'flex items-center space-x-3 p-2.5 border rounded-md hover:bg-gray-50 cursor-pointer text-sm text-gray-700';
        label.innerHTML = `
            <input type="checkbox" name="buku_ids[]" value="${buku.id}" ${isChecked} class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
            <span>${buku.nama_buku} <span class="text-xs text-gray-400">(${buku.tingkat_kelas})</span></span>
        `;
        container.appendChild(label);
    });
    
    document.getElementById('modal-edit-buku').classList.remove('hidden');
}

function closeEditBukuModal() {
    document.getElementById('modal-edit-buku').classList.add('hidden');
}

function confirmKembali(id, namaSiswa) {
    Swal.fire({
        title: 'Konfirmasi Pengembalian Buku',
        text: `Tandai buku paket atas nama "${namaSiswa}" telah dikembalikan hari ini?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#059669',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Sudah Dikembalikan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('form-kembali-' + id).submit();
        }
    });
}
</script>
@endsection

