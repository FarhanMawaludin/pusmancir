@extends('layouts.admin-app')

@section('content')
    <form method="POST" action="{{ route('admin.pengguna.update', $user->id) }}">
        @csrf
        @method('PUT')
        <div class="space-y-4">
            <h2 class="text-[28px] font-semibold text-text">Formulir Pengguna</h2>
            <div class=" border-gray-900/10 pb-8 p-4  bg-white border border-gray-200 rounded">
                <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">
                    <div class="sm:col-span-3">
                        <label for="name" class="block text-sm/6 font-medium text-text">Nama Pengguna</label>
                        <div class="mt-2">
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                                autocomplete="given-name"
                                class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text 
                                        border border-gray-300 placeholder:text-gray-400
                                        focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="country" class="block text-sm/6 font-medium text-text">Posisi</label>
                        <div class="mt-2 grid grid-cols-1">
                            <select id="role" name="role" autocomplete="role-name"
                            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text 
                            border border-gray-300 placeholder:text-gray-400
                            focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm">
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="pustakawan" {{ old('role', $user->role) == 'pustakawan' ? 'selected' : '' }}>Pustakawan</option>
                                <option value="anggota" {{ old('role', $user->role) == 'anggota' ? 'selected' : '' }}>Anggota</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-span-full">
                        <label for="username" class="block text-sm/6 font-medium text-text">Username</label>
                        <div class="mt-2">
                            <input id="username" name="username" type="username" value="{{ old('username', $user->username) }}"
                                autocomplete="username"
                                class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text 
                                        border border-gray-300 placeholder:text-gray-400
                                        focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm">
                            @error('username')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="col-span-full">
                        <label for="password" class="block text-sm/6 font-medium text-text">
                            Kata Sandi
                        </label>
                        <div class="mt-2">
                            <input type="password" name="password" id="password" autocomplete="password"
                                class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text 
                                        border border-gray-300 placeholder:text-gray-400
                                        focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm">
                                <p class="mt-1 text-sm text-gray-500">Biarkan kosong jika tidak ingin mengubah kata sandi</p>
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="col-span-full">
                        <label for="password_confirmation" class="block text-sm/6 font-medium text-text">
                            Konfirmasi Kata Sandi
                        </label>
                        <div class="mt-2">
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                autocomplete="password_confirmation"
                                class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text 
                                        border border-gray-300 placeholder:text-gray-400
                                        focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm">
                            @error('password_confirmation')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex items-center justify-start gap-x-4">
                    <button type="button"
                        class="text-sm/6 font-semibold text-text hover:text-text hover:border border-gray-900 rounded-md px-3 py-2 pointer"
                        onclick="location.href='{{ route('admin.pengguna.index') }}'">Batal</button>
                    <button type="submit"
                        class="bg-blue-700 rounded-md px-3 py-2 text-sm/6 font-semibold text-white shadow-xs hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Simpan</button>
                </div>
            </div>
        </div>
    </form>
@endsection
