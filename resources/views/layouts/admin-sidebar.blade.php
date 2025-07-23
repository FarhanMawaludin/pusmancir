<aside id="logo-sidebar"
    class="fixed top-0 left-0 z-40 w-64 h-full sm:h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 "
    aria-label="Sidebar">
    <div class="h-full px-3 pb-4 overflow-y-auto bg-white ">
        <ul class="space-y-2 font-medium ">
            <!-- Dashboard -->
            <li class="mb-4">
                <a href="{{ route('admin.dashboard.index') }}"
                    class="flex items-center p-2 rounded group {{ $activeMenu == 'dashboard' ? 'text-white bg-blue-700' : 'text-gray-400 hover:bg-gray-100  ' }}">
                    <svg class="w-5 h-5 {{ $activeMenu == 'dashboard' ? 'text-white' : 'text-base300 ' }}"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.143 4H4.857A.857.857 0 0 0 4 4.857v4.286c0 .473.384.857.857.857h4.286A.857.857 0 0 0 10 9.143V4.857A.857.857 0 0 0 9.143 4Zm10 0h-4.286a.857.857 0 0 0-.857.857v4.286c0 .473.384.857.857.857h4.286A.857.857 0 0 0 20 9.143V4.857A.857.857 0 0 0 19.143 4Zm-10 10H4.857a.857.857 0 0 0-.857.857v4.286c0 .473.384.857.857.857h4.286a.857.857 0 0 0 .857-.857v-4.286A.857.857 0 0 0 9.143 14Zm10 0h-4.286a.857.857 0 0 0-.857.857v4.286c0 .473.384.857.857.857h4.286a.857.857 0 0 0 .857-.857v-4.286a.857.857 0 0 0-.857-.857Z" />
                    </svg>
                    <span class="ml-3 {{ $activeMenu == 'dashboard' ? 'text-white' : 'text-base300' }}">Dashboard</span>
                </a>
            </li>

            <!-- Manajemen Data Pengguna -->
            <li class="text-xs px-2 text-text uppercase">Data Pengguna</li>

            @if (Auth::user()->role !== 'pustakawan')
                <li>
                    <a href="{{ route('admin.kelas.index') }}"
                        class="flex items-center p-2 rounded group {{ $activeMenu == 'kelas' ? 'text-white bg-blue-700' : 'text-gray-400 hover:bg-gray-100  ' }}">
                        <svg class="w-5 h-5 {{ $activeMenu == 'kelas' ? 'text-white' : 'text-gray-400 ' }}"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 4h12M6 4v16M6 4H5m13 0v16m0-16h1m-1 16H6m12 0h1M6 20H5M9 7h1v1H9V7Zm5 0h1v1h-1V7Zm-5 4h1v1H9v-1Zm5 0h1v1h-1v-1Zm-3 4h2a1 1 0 0 1 1 1v4h-4v-4a1 1 0 0 1 1-1Z" />
                        </svg>

                        <span class="ml-3 {{ $activeMenu == 'kelas' ? 'text-white' : 'text-base300' }}">Kelas</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.pengguna.index') }}"
                        class="flex items-center p-2 rounded group {{ $activeMenu == 'pengguna' ? 'text-white bg-blue-700' : 'text-gray-400 hover:bg-gray-100  ' }}">
                        <svg class="w-6 h-6 {{ $activeMenu == 'pengguna' ? 'text-white' : 'text-base300 ' }}"
                            fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2" stroke-linecap="square" stroke-linejoin="round">
                            <path
                                d="M10 19H5a1 1 0 0 1-1-1v-1a3 3 0 0 1 3-3h2m10 1a3 3 0 0 1-3 3m3-3a3 3 0 0 0-3-3m3 3h1m-4 3a3 3 0 0 1-3-3m3 3v1m-3-4a3 3 0 0 1 3-3m-3 3h-1m4-3v-1m-2.121 1.879-.707-.707m5.656 5.656-.707-.707m-4.242 0-.707.707m5.656-5.656-.707.707M12 8a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>


                        <span class="ml-3 {{ $activeMenu == 'pengguna' ? 'text-white' : 'text-base300' }}">Akun
                            Pengguna</span>
                    </a>
                </li>
            @endif

            <li class="mb-4">
                <button type="button"
                    class="flex items-center w-full p-2 text-base rounded group {{ $activeMenu == 'anggota' ? 'text-white bg-blue-700' : 'text-gray-400 hover:bg-gray-100  ' }}"
                    aria-controls="dropdown-example" data-collapse-toggle="dropdown-example-anggota">
                    <svg class="w-5 h-5 {{ $activeMenu == 'anggota' ? 'text-white' : 'text-gray-400 ' }}"
                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 6H5m2 3H5m2 3H5m2 3H5m2 3H5m11-1a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2M7 3h11a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1Zm8 7a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z" />
                    </svg>
                    <span
                        class="flex-1 ms-3  text-left {{ $activeMenu == 'anggota' ? 'text-white' : 'text-gray-400' }}
                        rtl:text-right whitespace-nowrap">Daftar
                        Anggota</span>
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 4 4 4-4" />
                    </svg>
                </button>
                <ul id="dropdown-example-anggota" class="hidden py-2 space-y-2">
                    <li>
                        <a href="{{ route('admin.anggota.index') }}"
                            class="flex items-center w-full p-2 text-gray-400 transition duration-75 rounded pl-11 group hover:bg-gray-100  ">
                            Aktif</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.anggota.indexAlumni') }}"
                            class="flex items-center w-full p-2 text-gray-400 transition duration-75 rounded pl-11 group hover:bg-gray-100  ">
                            Alumni</a>
                    </li>
                </ul>
            </li>


            <!-- Manajemen Data Inventori -->
            <li class="text-xs px-2 text-text uppercase">Data Inventori</li>

            <li class="mb-4">
                <button type="button"
                    class="flex items-center w-full p-2 text-base rounded group {{ $activeMenu == 'master' ? 'text-white bg-blue-700' : 'text-gray-400 hover:bg-gray-100  ' }}"
                    aria-controls="dropdown-example" data-collapse-toggle="dropdown-example">
                    <svg class="w-5 h-5 {{ $activeMenu == 'master' ? 'text-white' : 'text-gray-400 ' }}"
                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linejoin="round" stroke-width="2"
                            d="M10 12v1h4v-1m4 7H6a1 1 0 0 1-1-1V9h14v9a1 1 0 0 1-1 1ZM4 5h16a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z" />
                    </svg>


                    <span
                        class="flex-1 ms-3  text-left {{ $activeMenu == 'master' ? 'text-white' : 'text-gray-400' }}
                        rtl:text-right whitespace-nowrap">Data
                        Master</span>
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 4 4 4-4" />
                    </svg>
                </button>
                <ul id="dropdown-example" class="hidden py-2 space-y-2">
                    <li>
                        <a href="{{ route('admin.jenis-media.index') }}"
                            class="flex items-center w-full p-2 text-gray-400 transition duration-75 rounded pl-11 group hover:bg-gray-100  ">
                            Jenis Media</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.jenis-sumber.index') }}"
                            class="flex items-center w-full p-2 text-gray-400 transition duration-75 rounded pl-11 group hover:bg-gray-100  ">
                            Jenis Sumber</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.sumber.index') }}"
                            class="flex items-center w-full p-2 text-gray-400 transition duration-75 rounded pl-11 group hover:bg-gray-100  ">
                            Sumber</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.kategori-buku.index') }}"
                            class="flex items-center w-full p-2 text-gray-400 transition duration-75 rounded pl-11 group hover:bg-gray-100  ">
                            Kategori</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.penerbit.index') }}"
                            class="flex items-center w-full p-2 text-gray-400 transition duration-75 rounded pl-11 group hover:bg-gray-100  ">
                            Penerbit</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.sekolah.index') }}"
                            class="flex items-center w-full p-2 text-gray-400 transition duration-75 rounded pl-11 group hover:bg-gray-100  ">
                            Sekolah</a>
                    </li>
                </ul>
            </li>

            <li>
                <a href="{{ route('admin.inventori.index') }}"
                    class="flex items-center p-2 rounded group {{ $activeMenu == 'inventori' ? 'text-white bg-blue-700' : 'text-gray-400 hover:bg-gray-100  ' }}">
                    <svg class="w-5 h-5 {{ $activeMenu == 'inventori' ? 'text-white' : 'text-base300 ' }}"
                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 11v5m0 0 2-2m-2 2-2-2M3 6v1a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1Zm2 2v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V8H5Z" />
                    </svg>

                    <span class="ml-3 {{ $activeMenu == 'inventori' ? 'text-white' : 'text-base300' }}">Entri
                        Inventori</span>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.eksemplar.buku') }}"
                    class="flex items-center p-2 rounded group {{ $activeMenu == 'statusbuku' ? 'text-white bg-blue-700' : 'text-gray-400 hover:bg-gray-100  ' }}">
                    <svg class="w-5 h-5 {{ $activeMenu == 'statusbuku' ? 'text-white' : 'text-base300 ' }}"
                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                            d="M7.111 20A3.111 3.111 0 0 1 4 16.889v-12C4 4.398 4.398 4 4.889 4h4.444a.89.89 0 0 1 .89.889v12A3.111 3.111 0 0 1 7.11 20Zm0 0h12a.889.889 0 0 0 .889-.889v-4.444a.889.889 0 0 0-.889-.89h-4.389a.889.889 0 0 0-.62.253l-3.767 3.665a.933.933 0 0 0-.146.185c-.868 1.433-1.581 1.858-3.078 2.12Zm0-3.556h.009m7.933-10.927 3.143 3.143a.889.889 0 0 1 0 1.257l-7.974 7.974v-8.8l3.574-3.574a.889.889 0 0 1 1.257 0Z" />
                    </svg>
                    <span class="ml-3 {{ $activeMenu == 'statusbuku' ? 'text-white' : 'text-base300' }}">Status
                        Buku</span>
                </a>
            </li>


            <!-- Manajemen Data Buku -->
            <li class="text-xs px-2 text-text uppercase">Data Buku</li>

            <li class="mb-4">
                <button type="button"
                    class="flex items-center w-full p-2 text-base rounded group {{ $activeMenu == 'katalog' ? 'text-white bg-blue-700' : 'text-gray-400 hover:bg-gray-100  ' }}"
                    aria-controls="dropdown-example" data-collapse-toggle="dropdown-example-katalog">
                    <svg class="w-5 h-5 {{ $activeMenu == 'katalog' ? 'text-white' : 'text-gray-400 ' }}"
                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 19V4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v13H7a2 2 0 0 0-2 2Zm0 0a2 2 0 0 0 2 2h12M9 3v14m7 0v4" />
                    </svg>
                    <span
                        class="flex-1 ms-3  text-left {{ $activeMenu == 'katalog' ? 'text-white' : 'text-gray-400' }}
                        rtl:text-right whitespace-nowrap">Katalog</span>
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 4 4 4-4" />
                    </svg>
                </button>
                <ul id="dropdown-example-katalog" class="hidden py-2 space-y-2">
                    <li>
                        <a href="{{ route('admin.katalog.index') }}"
                            class="flex items-center w-full p-2 text-gray-400 transition duration-75 rounded pl-11 group hover:bg-gray-100  ">
                            Buku</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.paket.index') }}"
                            class="flex items-center w-full p-2 text-gray-400 transition duration-75 rounded pl-11 group hover:bg-gray-100  ">
                            Buku Paket</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.buku-elektronik.index') }}"
                            class="flex items-center w-full p-2 text-gray-400 transition duration-75 rounded pl-11 group hover:bg-gray-100  ">
                            Buku Elektronik</a>
                    </li>
                </ul>
            </li>

            <li>
                <a href="{{ route('admin.eksemplar.index') }}"
                    class="flex items-center p-2 rounded group {{ $activeMenu == 'eksemplar' ? 'text-white bg-blue-700' : 'text-gray-400 hover:bg-gray-100  ' }}">
                    <svg class="w-5 h-5 {{ $activeMenu == 'eksemplar' ? 'text-white' : 'text-base300 ' }}"
                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                            d="M2.9917 4.9834V18.917M9.96265 4.9834V18.917M15.9378 4.9834V18.917m2.9875-13.9336V18.917" />
                        <path stroke="currentColor" stroke-linecap="round"
                            d="M5.47925 4.4834V19.417m1.9917-14.9336V19.417M21.4129 4.4834V19.417M13.4461 4.4834V19.417" />
                    </svg>
                    <span class="ml-3 {{ $activeMenu == 'eksemplar' ? 'text-white' : 'text-base300' }}">
                        Cetak Barcode</span>
                </a>
            </li>

            <!-- Manajemen Peminjaman -->
            <li class="text-xs px-2 text-text uppercase">Data Peminjaman</li>

            <li class="mb-4">
                <button type="button"
                    class="flex items-center w-full p-2 text-base rounded group {{ $activeMenu == 'peminjaman' ? 'text-white bg-blue-700' : 'text-gray-400 hover:bg-gray-100  ' }}"
                    aria-controls="dropdown-example" data-collapse-toggle="dropdown-example-nonpaket">
                    <svg class="w-5 h-5 {{ $activeMenu == 'peminjaman' ? 'text-white' : 'text-gray-400 ' }}"
                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 3v4a1 1 0 0 1-1 1H5m4 6 2 2 4-4m4-8v16a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1Z" />
                    </svg>
                    <span
                        class="flex-1 ms-3  text-left {{ $activeMenu == 'peminjaman' ? 'text-white' : 'text-gray-400' }}
                        rtl:text-right whitespace-nowrap">Non
                        Paket</span>
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 4 4 4-4" />
                    </svg>
                </button>
                <ul id="dropdown-example-nonpaket" class="hidden py-2 space-y-2">
                    <li>
                        <a href="{{ route('admin.peminjaman.index') }}"
                            class="flex items-center w-full p-2 text-gray-400 transition duration-75 rounded pl-11 group hover:bg-gray-100  ">
                            Peminjaman</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.pengembalian.index') }}"
                            class="flex items-center w-full p-2 text-gray-400 transition duration-75 rounded pl-11 group hover:bg-gray-100  ">
                            Pengembalian</a>
                    </li>
                </ul>
            </li>

            <li class="mb-4">
                <button type="button"
                    class="flex items-center w-full p-2 text-base rounded group {{ $activeMenu == 'peminjamanPaket' ? 'text-white bg-blue-700' : 'text-gray-400 hover:bg-gray-100  ' }}"
                    aria-controls="dropdown-example" data-collapse-toggle="dropdown-example-paket">
                    <svg class="w-5 h-5 {{ $activeMenu == 'peminjamanPaket' ? 'text-white' : 'text-gray-400 ' }}"
                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 3v4a1 1 0 0 1-1 1H5m4 6 2 2 4-4m4-8v16a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1Z" />
                    </svg>
                    <span
                        class="flex-1 ms-3  text-left {{ $activeMenu == 'peminjamanPaket' ? 'text-white' : 'text-gray-400' }}
                        rtl:text-right whitespace-nowrap">Paket</span>
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 4 4 4-4" />
                    </svg>
                </button>
                <ul id="dropdown-example-paket" class="hidden py-2 space-y-2">
                    <li>
                        <a href="{{ route('admin.peminjaman-paket.index') }}"
                            class="flex items-center w-full p-2 text-gray-400 transition duration-75 rounded pl-11 group hover:bg-gray-100  ">
                            Peminjaman</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.pengembalian-paket.index') }}"
                            class="flex items-center w-full p-2 text-gray-400 transition duration-75 rounded pl-11 group hover:bg-gray-100  ">
                            Pengembalian</a>
                    </li>
                </ul>
            </li>

            <!-- Manajemen Peminjaman -->
            <li class="text-xs px-2 text-text uppercase">Data Pengunjung</li>

            <li class="mb-4">
                <button type="button"
                    class="flex items-center w-full p-2 text-base rounded group {{ $activeMenu == 'buku-tamu' ? 'text-white bg-blue-700' : 'text-gray-400 hover:bg-gray-100  ' }}"
                    aria-controls="dropdown-example" data-collapse-toggle="dropdown-example-tamu">
                    <svg class="w-5 h-5 {{ $activeMenu == 'buku-tamu' ? 'text-white' : 'text-gray-400 ' }}"
                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linejoin="round" stroke-width="2"
                            d="M12.1429 11v9m0-9c-2.50543-.7107-3.19099-1.39543-6.13657-1.34968-.48057.00746-.86348.38718-.86348.84968v7.2884c0 .4824.41455.8682.91584.8617 2.77491-.0362 3.45995.6561 6.08421 1.3499m0-9c2.5053-.7107 3.1067-1.39542 6.0523-1.34968.4806.00746.9477.38718.9477.84968v7.2884c0 .4824-.4988.8682-1 .8617-2.775-.0362-3.3758.6561-6 1.3499m2-14c0 1.10457-.8955 2-2 2-1.1046 0-2-.89543-2-2s.8954-2 2-2c1.1045 0 2 .89543 2 2Z" />
                    </svg>
                    <span
                        class="flex-1 ms-3  text-left {{ $activeMenu == 'buku-tamu' ? 'text-white' : 'text-gray-400' }}
                        rtl:text-right whitespace-nowrap">Tamu</span>
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 4 4 4-4" />
                    </svg>
                </button>
                <ul id="dropdown-example-tamu" class="hidden py-2 space-y-2">
                    <li>
                        <a href="{{ route('admin.buku-tamu.form') }}"
                            class="flex items-center w-full p-2 text-gray-400 transition duration-75 rounded pl-11 group hover:bg-gray-100  ">
                            Buku Tamu</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.buku-tamu.log-tamu') }}"
                            class="flex items-center w-full p-2 text-gray-400 transition duration-75 rounded pl-11 group hover:bg-gray-100  ">
                            Log Tamu</a>
                    </li>
                </ul>
            </li>

            @if (Auth::user()->role !== 'pustakawan')
                <li class="text-xs px-2 text-text uppercase">Data Manajemen Surat</li>

                <li class="mb-4">
                    <button type="button"
                        class="flex items-center w-full p-2 text-base rounded group {{ $activeMenu == 'master-surat' ? 'text-white bg-blue-700' : 'text-gray-400 hover:bg-gray-100  ' }}"
                        aria-controls="dropdown-example" data-collapse-toggle="dropdown-example-surat">
                        <svg class="w-5 h-5 {{ $activeMenu == 'master-surat' ? 'text-white' : 'text-gray-400 ' }}"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2"
                                d="M13.5 8H4m0-2v13a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V9a1 1 0 0 0-1-1h-5.032a1 1 0 0 1-.768-.36l-1.9-2.28a1 1 0 0 0-.768-.36H5a1 1 0 0 0-1 1Z" />
                        </svg>

                        <span
                            class="flex-1 ms-3  text-left {{ $activeMenu == 'master-surat' ? 'text-white' : 'text-gray-400' }}
                        rtl:text-right whitespace-nowrap">Master
                            Surat</span>
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="m1 1 4 4 4-4" />
                        </svg>
                    </button>
                    <ul id="dropdown-example-surat" class="hidden py-2 space-y-2">
                        <li>
                            <a href="{{ route('admin.kode-jenis-surat.index') }}"
                                class="flex items-center w-full p-2 text-gray-400 transition duration-75 rounded pl-11 group hover:bg-gray-100  ">
                                Jenis Surat</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.instansi.index') }}"
                                class="flex items-center w-full p-2 text-gray-400 transition duration-75 rounded pl-11 group hover:bg-gray-100  ">
                                Instansi</a>
                        </li>
                    </ul>
                </li>

                <li>
                    <a href="{{ route('admin.surat-keluar.index') }}"
                        class="flex items-center p-2 rounded group {{ $activeMenu == 'surat-keluar' ? 'text-white bg-blue-700' : 'text-gray-400 hover:bg-gray-100  ' }}">
                        <svg class="w-6 h-6 {{ $activeMenu == 'surat-keluar' ? 'text-white' : 'text-base300 ' }}"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 15v2a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-2M12 4v12m0-12 4 4m-4-4L8 8" />
                        </svg>


                        <span class="ml-3 {{ $activeMenu == 'surat-keluar' ? 'text-white' : 'text-base300' }}">Surat
                            Keluar</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.surat-masuk.index') }}"
                        class="flex items-center p-2 rounded group {{ $activeMenu == 'surat-masuk' ? 'text-white bg-blue-700' : 'text-gray-400 hover:bg-gray-100  ' }}">
                        <svg class="w-6 h-6 {{ $activeMenu == 'surat-masuk' ? 'text-white' : 'text-base300 ' }}"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 15v2a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-2m-8 1V4m0 12-4-4m4 4 4-4" />
                        </svg>
                        <span class="ml-3 {{ $activeMenu == 'surat-masuk' ? 'text-white' : 'text-base300' }}">Surat
                            Masuk</span>
                    </a>
                </li>


                <li class="text-xs px-2 text-text uppercase">Data Manajemen Berita</li>

                <li>
                    <a href="{{ route('admin.berita.index') }}"
                        class="flex items-center p-2 rounded group {{ $activeMenu == 'berita' ? 'text-white bg-blue-700' : 'text-gray-400 hover:bg-gray-100  ' }}">
                        <svg class="w-6 h-6 {{ $activeMenu == 'berita' ? 'text-white' : 'text-base300 ' }}"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 7h1v12a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1V5a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h11.5M7 14h6m-6 3h6m0-10h.5m-.5 3h.5M7 7h3v3H7V7Z" />
                        </svg>
                        <span class="ml-3 {{ $activeMenu == 'berita' ? 'text-white' : 'text-base300' }}">Berita</span>
                    </a>
                </li>
            @endif


            <!-- Logout -->
            <li class="mt-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        onclick="event.preventDefault(); localStorage.removeItem('auth_token'); this.closest('form').submit();"
                        class="w-full flex items-center p-2 text-red-600 rounded hover:bg-red-200 group cursor-pointer">
                        <svg class="w-6 h-6 text-red-600 " fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2"
                                d="M20 12H8m12 0-4 4m4-4-4-4M9 4H7a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h2" />
                        </svg>
                        <span class="ml-3 text-left">Keluar</span>
                    </button>
                </form>
            </li>
        </ul>
    </div>
</aside>
