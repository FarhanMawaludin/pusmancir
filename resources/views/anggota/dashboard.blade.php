@extends('layouts.anggota-app')

@section('content')
    <div class="md:mt-28 sm:mt-40 text-center">
        <!-- Header -->
        <h1 class="text-2xl sm:text-3xl font-semibold mb-2">
            Selamat Datang,
            <span class="text-blue-700 font-bold hover:underline cursor-pointer">{{ auth()->user()->name }}</span> 👋
        </h1>
        <p class="text-gray-500 mb-6 text-sm sm:text-base">
            Lengkapi profilmu sekarang dan lakukan peminjaman buku.
        </p>

        <div class="flex justify-center items-center">
            <a href="{{ route('anggota.profil.index') }}"
                class="btn bg-blue-700 hover:bg-blue-800 text-white font-semibold px-4 py-2 rounded">Lengkapi Profil</a>
        </div>
    </div>
@endsection

@push('scripts')
    @if (session('force_password_change'))
        <script>
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan keamanan ubah kata sandi',
                text: '{{ session('warning') }}',
                confirmButtonText: 'Ubah Sekarang',
                confirmButtonColor: '#eab308',
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('anggota.profil.edit', ['id' => Auth::user()->id]) }}";
                }
            });
        </script>
    @endif
@endpush
