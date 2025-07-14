<aside id="logo-sidebar"
    class="fixed top-0 left-0 z-40 w-64 h-full sm:h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 "
    aria-label="Sidebar">
    <div class="h-full px-3 pb-4 overflow-y-auto bg-white ">
        <ul class="space-y-2 font-medium ">
            <!-- Dashboard -->
            <li class="mb-4">
                <a href="{{ route('anggota.dashboard.index') }}"
                    class="flex items-center p-2 rounded group {{ $activeMenu == 'dashboard' ? 'text-white bg-blue-700' : 'text-gray-900 hover:bg-gray-100  ' }}">
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

            <li class="text-xs px-2 text-text uppercase">Manajemen Peminjaman</li>

            <li>
                <a href="{{ route('anggota.katalog.index') }}"
                    class="flex items-center p-2 rounded group {{ $activeMenu == 'katalog' ? 'text-white bg-blue-700' : 'text-gray-900 hover:bg-gray-100  ' }}">
                    <svg class="w-5 h-5 {{ $activeMenu == 'katalog' ? 'text-white' : 'text-base300 ' }}"
                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 19V4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v13H7a2 2 0 0 0-2 2Zm0 0a2 2 0 0 0 2 2h12M9 3v14m7 0v4" />
                    </svg>
                    <span class="ml-3 {{ $activeMenu == 'katalog' ? 'text-white' : 'text-base300' }}">Katalog
                        Buku</span>
                </a>
            </li>

            <li>
                <a href="{{ route('anggota.peminjaman.index') }}"
                    class="flex items-center p-2 rounded group {{ $activeMenu == 'peminjaman' ? 'text-white bg-blue-700' : 'text-gray-900 hover:bg-gray-100  ' }}">
                    <svg class="w-5 h-5 {{ $activeMenu == 'peminjaman' ? 'text-white' : 'text-base300 ' }}"
                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 7.757v8.486M7.757 12h8.486M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>

                    <span
                        class="ml-3 {{ $activeMenu == 'peminjaman' ? 'text-white' : 'text-base300' }}">Peminjaman</span>
                </a>
            </li>



            <!-- Logout -->
            <li class="mt-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        onclick="event.preventDefault(); localStorage.removeItem('auth_token'); this.closest('form').submit();"
                        class="w-full flex items-center p-2 text-red-600 rounded hover:bg-red-200 group cursor-pointer">
                        <svg class="w-6 h-6 text-red-600 " fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 12H8m12 0-4 4m4-4-4-4M9 4H7a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h2" />
                        </svg>
                        <span class="ml-3 text-left">Keluar</span>
                    </button>
                </form>
            </li>
        </ul>
    </div>
</aside>
