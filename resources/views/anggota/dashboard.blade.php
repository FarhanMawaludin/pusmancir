dashboard anggota

<li class="mt-4">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit"
            onclick="event.preventDefault(); localStorage.removeItem('auth_token'); this.closest('form').submit();"
            class="w-full flex items-center p-2 text-red-600 rounded-lg hover:bg-red-200 group cursor-pointer">
            <svg class="w-6 h-6 text-red-600 dark:text-white" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                    stroke-width="2"
                    d="M20 12H8m12 0-4 4m4-4-4-4M9 4H7a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h2" />
            </svg>
            <span class="ml-3 text-left">Keluar</span>
        </button>
    </form>
</li>