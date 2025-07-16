@extends('layouts.anggota-app')

@section('content')
    <form method="POST" action="{{ route('anggota.profil.update', $user->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <h2 class="text-[28px] font-semibold text-text mb-4">Edit Profil</h2>
        <div class="border-b border-gray-900/10 pb-12 p-6 bg-white border border-gray-200 rounded">
            <div class="mt-4 grid grid-cols-1 sm:grid-cols-6 gap-x-6 gap-y-8">

                {{-- Nama Lengkap --}}
                <div class="sm:col-span-3">
                    <label for="name" class="block text-sm font-medium text-text">Nama Lengkap</label>
                    <div class="mt-2">
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                            autocomplete="given-name" readonly
                            class="block w-full rounded-md bg-gray-100 px-3 py-1.5 text-base text-gray-500 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-none sm:text-sm">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- NISN --}}
                <div class="sm:col-span-3">
                    <label for="nisn" class="block text-sm font-medium text-text">NISN</label>
                    <div class="mt-2">
                        <input type="text" name="nisn" id="nisn"
                            value="{{ old('nisn', $user->anggota->nisn ?? '') }}"
                            class="block w-full rounded-md bg-gray-100 px-3 py-1.5 text-base text-gray-500 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-none sm:text-sm">
                        @error('nisn')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Email --}}
                <div class="sm:col-span-3">
                    <label for="email" class="block text-sm font-medium text-text">Email</label>
                    <div class="mt-2">
                        <input type="email" name="email" id="email"
                            value="{{ old('email', $user->anggota->email ?? '') }}"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 sm:text-sm">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- No Telepon --}}
                <div class="sm:col-span-3">
                    <label for="no_telp" class="block text-sm font-medium text-text">No Telepon (WhatsApp)</label>
                    <div class="mt-2">
                        <input type="text" name="no_telp" id="no_telp"
                            value="{{ old('no_telp', $user->anggota->no_telp ?? '') }}"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 sm:text-sm"
                            oninput="formatNoTelp(this)">

                        @error('no_telp')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{--  Kelas --}}
                <div class="sm:col-span-3">
                    <label for="kelas_id" class="block text-sm font-medium text-text">Kelas</label>
                    <select id="kelas_id" name="kelas_id"
                        class="mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-base text-text outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 sm:text-sm">
                        @foreach ($kelas as $item)
                            <option value="{{ $item->id }}"
                                {{ old('kelas_id', optional($user->anggota)->kelas_id) == $item->id ? 'selected' : '' }}>
                                {{ $item->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                    @error('kelas_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Foto Upload --}}
                <div class="col-span-full sm:col-span-3">
                    <label for="foto" class="block text-sm font-medium text-text">Upload Foto Profil</label>
                    <input type="file" id="foto" name="foto" accept="image/*"
                        class="mt-2 block w-full text-sm text-text border border-gray-300 rounded cursor-pointer bg-gray-50 focus:outline-none">
                    <p class="mt-1 text-sm text-gray-500">JPG, PNG, atau GIF. Max 2MB.</p>
                    @error('foto')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="col-span-full sm:col-span-6">
                    <label for="password" class="block text-sm font-medium text-text">Kata Sandi</label>
                    <div class="mt-2">
                        <input type="password" name="password" id="password"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 sm:text-sm">
                        <p class="mt-1 text-sm text-gray-500">Biarkan kosong jika tidak ingin mengubah kata sandi</p>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Password Confirmation --}}
                <div class="col-span-full sm:col-span-6">
                    <label for="password_confirmation" class="block text-sm font-medium text-text">Konfirmasi Kata
                        Sandi</label>
                    <div class="mt-2">
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 sm:text-sm">
                        @error('password_confirmation')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Tombol --}}
            <div class="mt-6 flex items-center justify-start gap-x-4">
                <a href="{{ route('anggota.profil.index') }}"
                    class="text-sm font-semibold text-text hover:border border-gray-900 rounded-md px-3 py-2">
                    Batal
                </a>
                <button type="submit"
                    class="!bg-indigo-600 hover:bg-indigo-500 rounded-md px-3 py-2 text-sm font-semibold text-white shadow-sm focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </form>

    <script>
        function formatNoTelp(input) {
            let val = input.value.trim();
            if (val.startsWith('0')) {
                input.value = val.replace(/^0/, '+62');
            }
        }
    </script>
@endsection
