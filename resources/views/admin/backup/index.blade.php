@extends('layouts.admin-app')

@section('content')
<div class="max-w-3xl mt-8 px-4">
    <!-- Judul -->
    <h1 class="text-2xl font-bold text-text mb-6">Backup Database</h1>

    <!-- Tombol Backup Database: lebar auto, tidak full -->
    <button
        onclick="location.href='{{ route('admin.backup.run') }}'"
        type="button"
        class="inline-flex justify-center items-center gap-2 text-white bg-blue-700 hover:bg-blue-800 
               focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded 
               text-sm px-4 py-2.5 text-center dark:bg-blue-700 dark:hover:bg-blue-800 dark:focus:ring-blue-800"
        style="width: auto;"
    >
        <span>Backup Database</span>
    </button>

    <!-- Form Import Database: flex row, align center, gap, tapi input & label vertikal -->
    <form action="{{ route('admin.backup.import') }}" method="POST" enctype="multipart/form-data" class="mt-6 flex items-end gap-6">
        @csrf
    
        <!-- Upload file area -->
        <div class="w-1/2 flex flex-col">
            <label for="sql_file" class="block text-sm font-medium text-text mb-2">Upload File SQL</label>
    
            <label for="sql_file" 
                class="flex items-center cursor-pointer rounded-md overflow-hidden border border-gray-300 bg-white w-full">
                <span class="bg-gray-800 text-white text-sm font-semibold px-4 py-2">
                    Pilih File
                </span>
                <span id="file_name" class="ml-3 text-sm text-gray-500 truncate">
                    Tidak ada file dipilih
                </span>
            </label>
    
            <input type="file" name="sql_file" id="sql_file" accept=".sql,.txt" class="hidden" required>
    
            @error('sql_file')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    
        <!-- Tombol Import -->
        <button type="submit" 
            class="bg-green-600 hover:bg-green-700 text-white font-semibold rounded px-6 py-2 transition whitespace-nowrap"
            style="width: auto; margin-top: 26px;" 
        >
            Import Database
        </button>
    </form>
    
</div>

<script>
    document.getElementById('sql_file').addEventListener('change', function(e) {
        const fileName = e.target.files.length ? e.target.files[0].name : 'Tidak ada file dipilih';
        document.getElementById('file_name').textContent = fileName;
    });
</script>
@endsection
