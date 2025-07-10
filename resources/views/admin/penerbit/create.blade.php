@extends('layouts.admin-app')

@section('content')
    <form method="POST" action="{{ route('admin.penerbit.store') }}">
        @csrf
        <div class="space-y-4">
            <h2 class="text-[28px] font-semibold text-text">Formulir Penerbit</h2>
            <div class=" border-gray-900/10 pb-8 p-4  bg-white border border-gray-200 rounded">
                <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">
                    <div class="sm:col-span-full">
                        <label for="nama_kelas" class="block text-sm/6 font-medium text-text">Nama Penerbit</label>
                        <div class="mt-2">
                            <input type="text" name="nama_penerbit" id="nama_penerbit" value="{{ old('nama_penerbit') }}"
                                autocomplete="given-name"
                                class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-text outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
                                style="text-transform: uppercase;">
                            @error('nama_penerbit')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-start gap-x-4">
                    <button type="button"
                        class="text-sm/6 font-semibold text-text hover:text-text hover:border border-gray-900 rounded-md px-3 py-2 pointer"
                        onclick="location.href='{{ route('admin.penerbit.index') }}'">Batal</button>
                    <button type="submit"
                        class="bg-blue-700 rounded-md px-3 py-2 text-sm/6 font-semibold text-white shadow-xs hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Simpan</button>
                </div>
            </div>
        </div>
    </form>
@endsection
