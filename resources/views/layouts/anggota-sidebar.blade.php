<aside id="logo-sidebar"
    class="fixed top-0 left-0 z-40 w-64 h-full sm:h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 "
    aria-label="Sidebar">
    <div class="h-full px-3 pb-4 overflow-y-auto bg-white ">
        <ul class="space-y-2 font-medium ">
            <!-- Dashboard -->
            <li class="mb-4">
                <a href="{{ route('anggota.dashboard.index') }}"
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
            <li class="text-xs px-2 text-text uppercase">Manajemen Akun</li>

            <li class="mb-4">
                <a href="{{ route('anggota.profil.index') }}"
                    class="flex items-center p-2 rounded group {{ $activeMenu == 'profil' ? 'text-white bg-blue-700' : 'text-gray-900 hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700' }}">
                    <svg class="w-5 h-5 {{ $activeMenu == 'profil' ? 'text-white' : 'text-gray-400 dark:text-white' }}"
                        fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 12c2.21 0 4-1.79 4-4S14.21 4 12 4 8 5.79 8 8s1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                    </svg>
                    <span class="ml-3 {{ $activeMenu == 'profil' ? 'text-white' : 'text-gray-400' }}">Profil</span>
                </a>
            </li>

            <li class="text-xs px-2 text-text uppercase">Manajemen Buku</li>

            <li class="mb-4">
                <button type="button"
                    class="flex items-center w-full p-2 text-base rounded group {{ $activeMenu == 'katalog' ? 'text-white bg-blue-700' : 'text-gray-900 hover:bg-gray-100  ' }}"
                    aria-controls="dropdown-example" data-collapse-toggle="dropdown-example-katalog">
                    <svg class="w-5 h-5 {{ $activeMenu == 'katalog' ? 'text-white' : 'text-gray-400 ' }}"
                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linejoin="round" stroke-width="2"
                            d="M12.1429 11v9m0-9c-2.50543-.7107-3.19099-1.39543-6.13657-1.34968-.48057.00746-.86348.38718-.86348.84968v7.2884c0 .4824.41455.8682.91584.8617 2.77491-.0362 3.45995.6561 6.08421 1.3499m0-9c2.5053-.7107 3.1067-1.39542 6.0523-1.34968.4806.00746.9477.38718.9477.84968v7.2884c0 .4824-.4988.8682-1 .8617-2.775-.0362-3.3758.6561-6 1.3499m2-14c0 1.10457-.8955 2-2 2-1.1046 0-2-.89543-2-2s.8954-2 2-2c1.1045 0 2 .89543 2 2Z" />
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
                        <a href="{{ route('anggota.katalog.index') }}"
                            class="flex items-center w-full p-2 text-gray-400 transition duration-75 rounded pl-11 group hover:bg-gray-100  ">
                            Non Paket</a>
                    </li>
                    <li>
                        <a href="{{ route('anggota.katalog-paket.index') }}"
                            class="flex items-center w-full p-2 text-gray-400 transition duration-75 rounded pl-11 group hover:bg-gray-100  ">
                            Paket</a>
                    </li>
                </ul>
            </li>

            <li class="text-xs px-2 text-text uppercase">Manajemen Peminjaman</li>

            <li class="mb-4">
                <button type="button"
                    class="flex items-center w-full p-2 text-base rounded group {{ $activeMenu == 'peminjaman' ? 'text-white bg-blue-700' : 'text-gray-900 hover:bg-gray-100  ' }}"
                    aria-controls="dropdown-example" data-collapse-toggle="dropdown-example-peminjaman">
                    <svg class="w-5 h-5 {{ $activeMenu == 'peminjaman' ? 'text-white' : 'text-gray-400 ' }}"
                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 3v4a1 1 0 0 1-1 1H5m4 6 2 2 4-4m4-8v16a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1Z" />
                    </svg>
                    <span
                        class="flex-1 ms-3  text-left {{ $activeMenu == 'peminjaman' ? 'text-white' : 'text-gray-400' }}
                        rtl:text-right whitespace-nowrap">Peminjaman</span>
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 4 4 4-4" />
                    </svg>
                </button>
                <ul id="dropdown-example-peminjaman" class="hidden py-2 space-y-2">
                    <li>
                        <a href="{{ route('anggota.peminjaman.index') }}"
                            class="flex items-center w-full p-2 text-gray-400 transition duration-75 rounded pl-11 group hover:bg-gray-100  ">
                            Non Paket</a>
                    </li>
                    <li>
                        <a href="{{ route('anggota.peminjaman-paket.index') }}"
                            class="flex items-center w-full p-2 text-gray-400 transition duration-75 rounded pl-11 group hover:bg-gray-100  ">
                            Paket</a>
                    </li>
                </ul>
            </li>

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
