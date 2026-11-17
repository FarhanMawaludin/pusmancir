{{--
    Field tambahan untuk anggota kategori GURU (NIP / NIK).
    Variabel opsional: $anggota (model Anggota saat edit)
--}}
@php
    $anggota = $anggota ?? null;
    $inputClass = 'block w-full rounded-md bg-white px-3 py-1.5 text-base text-text border border-gray-300 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 sm:text-sm';
    $jenisIdentitas = old('jenis_identitas', $anggota && $anggota->isGuru() ? $anggota->jenis_identitas : 'NIP');
@endphp

<div class="sm:col-span-3 guru-field" style="display:none">
    <label for="jenis_identitas" class="block text-sm/6 font-medium text-text">Jenis Identitas Guru</label>
    <div class="mt-2">
        <select id="jenis_identitas" name="jenis_identitas" class="{{ $inputClass }}">
            <option value="NIP" {{ $jenisIdentitas === 'NIP' ? 'selected' : '' }}>NIP (Nomor Induk Pegawai)</option>
            <option value="NIK" {{ $jenisIdentitas === 'NIK' ? 'selected' : '' }}>NIK (Nomor Induk Kependudukan)</option>
        </select>
        @error('jenis_identitas')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="sm:col-span-3 guru-field" style="display:none">
    <label for="no_telp" class="block text-sm/6 font-medium text-text">No. Telepon / WhatsApp <span class="text-gray-400 font-normal">(opsional)</span></label>
    <div class="mt-2">
        <input type="text" name="no_telp" id="no_telp" value="{{ old('no_telp', $anggota->no_telp ?? '') }}"
            placeholder="08xxxxxxxxxx" class="{{ $inputClass }}">
        @error('no_telp')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="sm:col-span-3 guru-field" style="display:none">
    <label for="email" class="block text-sm/6 font-medium text-text">Email <span class="text-gray-400 font-normal">(opsional)</span></label>
    <div class="mt-2">
        <input type="email" name="email" id="email" value="{{ old('email', $anggota->email ?? '') }}"
            placeholder="nama@sekolah.sch.id" class="{{ $inputClass }}">
        @error('email')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="col-span-full guru-field" style="display:none">
    <div class="rounded-md p-3 text-sm" style="background-color:#eff6ff; color:#1e40af; border:1px solid #bfdbfe;">
        <strong>Catatan:</strong> Guru disimpan sebagai anggota perpustakaan sehingga dapat meminjam buku.
        Username login guru adalah <strong>Nomor NIP/NIK</strong>, dan nomor ini juga dipakai sebagai ID kartu anggota (QR).
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('role');
        const kelasField = document.getElementById('kelas-field');
        const usernameLabel = document.querySelector('label[for="username"]');
        const usernameInput = document.getElementById('username');
        const jenisIdentitas = document.getElementById('jenis_identitas');

        function syncLabel() {
            if (roleSelect.value === 'guru') {
                usernameLabel.textContent = 'Nomor ' + jenisIdentitas.value + ' (dipakai sebagai username login)';
                usernameInput.placeholder = jenisIdentitas.value === 'NIK' ? '16 digit NIK' : '18 digit NIP';
                usernameInput.setAttribute('inputmode', 'numeric');
            } else if (roleSelect.value === 'anggota') {
                usernameLabel.textContent = 'Username (NISN)';
                usernameInput.placeholder = 'NISN siswa';
                usernameInput.removeAttribute('inputmode');
            } else {
                usernameLabel.textContent = 'Username';
                usernameInput.placeholder = '';
                usernameInput.removeAttribute('inputmode');
            }
        }

        function toggleFields() {
            const isGuru = roleSelect.value === 'guru';
            document.querySelectorAll('.guru-field').forEach(el => el.style.display = isGuru ? '' : 'none');
            if (kelasField) kelasField.style.display = roleSelect.value === 'anggota' ? '' : 'none';
            syncLabel();
        }

        toggleFields();
        roleSelect.addEventListener('change', toggleFields);
        jenisIdentitas.addEventListener('change', syncLabel);
    });
</script>
