<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Anggota;
use App\Models\Kelas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Validator;

class PenggunaController extends Controller
{
    public function index(Request $request)
    {

        $activeMenu = "pengguna";

        $search = $request->input('search');
        $category = $request->input('category', 'all');

        $query = User::with('anggota.kelas');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhereHas('anggota', fn($q2) => $q2->where('nisn', 'like', "%{$search}%"));
            });
        }

        if ($category === 'guru') {
            $query->where('role', 'anggota')
                ->whereHas('anggota', fn($q) => $q->where('jenis_anggota', 'guru'));
        } elseif ($category === 'siswa') {
            $query->where('role', 'anggota')
                ->whereDoesntHave('anggota', fn($q) => $q->where('jenis_anggota', 'guru'));
        } elseif ($category !== 'all') {
            $query->where('role', $category);
        }

        // CASE dipakai agar kompatibel MySQL & SQLite (FIELD() hanya ada di MySQL)
        $query->orderByRaw("CASE role WHEN 'admin' THEN 1 WHEN 'pustakawan' THEN 2 ELSE 3 END")
            ->orderBy('created_at', 'asc');

        $user = $query->paginate(10)->appends([
            'search' => $search,
            'category' => $category
        ]);

        $list_kelas = Kelas::orderBy('nama_kelas', 'asc')->get();

        return view('admin.pengguna.index', [
            'activeMenu' => $activeMenu,
            'user' => $user,
            'category' => $category,
            'search' => $search,
            'list_kelas' => $list_kelas,
        ]);
    }

    public function create(Request $request)
    {
        $activeMenu = "pengguna";
        $list_kelas = Kelas::orderBy('nama_kelas', 'asc')->get();
        return view('admin.pengguna.create', [
            'activeMenu' => $activeMenu,
            'list_kelas' => $list_kelas,
            'defaultRole' => $request->query('jenis') === 'guru' ? 'guru' : 'anggota',
        ]);
    }

    /** Aturan validasi tambahan untuk guru (NIP/NIK). */
    private function rulesGuru(?int $anggotaId = null): array
    {
        return [
            'jenis_identitas' => ['required', Rule::in(['NIP', 'NIK'])],
            'no_telp' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('anggota', 'email')->ignore($anggotaId)],
        ];
    }

    private function pesanValidasi(bool $isGuru): array
    {
        return [
            'name.required' => 'Nama tidak boleh kosong.',
            'username.required' => $isGuru ? 'NIP/NIK wajib diisi.' : 'Username wajib diisi.',
            'username.unique' => $isGuru ? 'NIP/NIK sudah terdaftar.' : 'Username sudah digunakan sebelumnya.',
            'username.regex' => 'NIP/NIK hanya boleh berisi angka.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role.required' => 'Posisi wajib dipilih.',
            'role.in' => 'Posisi yang dipilih tidak valid.',
            'kelas_id.exists' => 'Kelas yang dipilih tidak valid.',
            'jenis_identitas.required' => 'Jenis identitas (NIP/NIK) wajib dipilih.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan anggota lain.',
        ];
    }

    /**
     * Membuat record anggota untuk guru. Nomor NIP/NIK disimpan di kolom `nisn`
     * (kolom identitas anggota) supaya proses peminjaman, scan kartu & buku tamu
     * yang sudah ada tetap berfungsi untuk guru.
     */
    private function buatAnggotaGuru(User $user, string $nomor, string $jenisIdentitas, ?string $noTelp = null, ?string $email = null): Anggota
    {
        return Anggota::create([
            'user_id' => $user->id,
            'no_telp' => $noTelp,
            'email' => $email,
            'kelas_id' => null,
            'nisn' => $nomor,
            'qr_code' => Anggota::where('qr_code', $nomor)->exists() ? null : $nomor,
            'status' => 'aktif',
            'jenis_anggota' => 'guru',
            'jenis_identitas' => strtoupper($jenisIdentitas),
        ]);
    }

    public function store(Request $request)
    {
        $isGuru = $request->input('role') === 'guru';

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'], // username = NISN (siswa) / NIP-NIK (guru)
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['admin', 'pustakawan', 'anggota', 'guru'])],
            'kelas_id' => ['nullable', 'exists:kelas,id'],
        ];

        if ($isGuru) {
            $rules['username'][] = 'regex:/^[0-9]+$/';
            $rules['username'][] = Rule::unique('anggota', 'nisn');
            $rules += $this->rulesGuru();
        }

        $validated = $request->validate($rules, $this->pesanValidasi($isGuru));

        try {
            DB::transaction(function () use ($validated, $isGuru) {
                // Guru disimpan sebagai role "anggota" agar bisa meminjam buku
                $user = User::create([
                    'name' => $validated['name'],
                    'username' => $validated['username'],
                    'password' => Hash::make($validated['password']),
                    'role' => $isGuru ? 'anggota' : $validated['role'],
                ]);

                if ($isGuru) {
                    $this->buatAnggotaGuru(
                        $user,
                        $validated['username'],
                        $validated['jenis_identitas'],
                        $validated['no_telp'] ?? null,
                        $validated['email'] ?? null
                    );
                } elseif ($validated['role'] === 'anggota') {
                    // Gunakan username sebagai NISN dan QR code (karena diasumsikan unik)
                    $nisn = $validated['username'];

                    Anggota::create([
                        'user_id' => $user->id,
                        'no_telp' => null,
                        'email' => null,
                        'kelas_id' => $validated['kelas_id'] ?? null,
                        'nisn' => $nisn,
                        'qr_code' => $nisn, // Simpan NISN sebagai QR code
                        'jenis_anggota' => 'siswa',
                        'jenis_identitas' => 'NISN',
                    ]);
                }
            });

            return redirect()->route('admin.pengguna.index', $isGuru ? ['category' => 'guru'] : [])
                ->with('success', $isGuru ? 'Data guru berhasil ditambahkan.' : 'Pengguna berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan pengguna. Pesan: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $user = User::with('anggota.kelas')->findOrFail($id);
        $activeMenu = 'pengguna';
        $list_kelas = Kelas::orderBy('nama_kelas', 'asc')->get();
        return view('admin.pengguna.edit', [
            'activeMenu' => $activeMenu,
            'user' => $user,
            'list_kelas' => $list_kelas,
        ]);
    }

    public function update(Request $request, $id)
    {
        $isGuru = $request->input('role') === 'guru';
        $user = User::with('anggota')->findOrFail($id);
        $anggotaId = optional($user->anggota)->id;

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['admin', 'pustakawan', 'anggota', 'guru'])],
            'kelas_id' => ['nullable', 'exists:kelas,id'],
        ];

        if ($isGuru) {
            $rules['username'][] = 'regex:/^[0-9]+$/';
            $rules['username'][] = Rule::unique('anggota', 'nisn')->ignore($anggotaId);
            $rules += $this->rulesGuru($anggotaId);
        }

        $validated = $request->validate($rules, $this->pesanValidasi($isGuru));

        try {
            $updateData = [
                'name' => $validated['name'],
                'username' => $validated['username'],
                'role' => $isGuru ? 'anggota' : $validated['role'],
            ];

            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $user->update($updateData);

            if ($isGuru) {
                $dataGuru = [
                    'nisn' => $validated['username'],
                    'kelas_id' => null,
                    'jenis_anggota' => 'guru',
                    'jenis_identitas' => $validated['jenis_identitas'],
                    'no_telp' => $validated['no_telp'] ?? null,
                    'email' => $validated['email'] ?? null,
                ];
                if ($user->anggota) {
                    $user->anggota->update($dataGuru);
                } else {
                    $this->buatAnggotaGuru($user, $validated['username'], $validated['jenis_identitas'], $dataGuru['no_telp'], $dataGuru['email']);
                }
            } elseif ($user->role === 'anggota' && $user->anggota) {
                // Update kelas di data anggota jika role anggota (siswa)
                $user->anggota->update([
                    'kelas_id' => $validated['kelas_id'] ?? null,
                    'jenis_anggota' => 'siswa',
                    'jenis_identitas' => 'NISN',
                ]);
            }

            return redirect()->route('admin.pengguna.index')
                ->with('success', 'Pengguna berhasil diupdate.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'error' => 'Terjadi kesalahan saat mengupdate pengguna: ' . $e->getMessage()
                ]);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);

            // Hapus data anggota terkait terlebih dahulu
            if ($user->anggota) {
                $user->anggota->delete();
            }

            $user->delete();
            return redirect()->route('admin.pengguna.index')
                ->with('success', 'Pengguna berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus pengguna.');
        }
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        $activeMenu = 'pengguna';
        return view('admin.pengguna.show', [
            'activeMenu' => $activeMenu,
            'user' => $user
        ]);
    }


    public function import(Request $request)
    {
        $request->validate([
            'file_pengguna' => 'required|file|mimes:xlsx,xls',
        ]);

        $file = $request->file('file_pengguna');
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $jumlahBerhasil = 0;
        $jumlahDuplikat = 0;

        foreach (array_slice($rows, 1) as $row) {
            $name = $row[0] ?? null;
            $username = $row[1] ?? null;
            $password = $row[2] ?? null;
            $kelas_nama = $row[3] ?? null;

            // Cek jika ada data penting yang kosong
            if (!$name || !$username || !$password) {
                continue;
            }

            // Cek apakah username sudah ada
            if (User::where('username', $username)->exists()) {
                $jumlahDuplikat++;
                continue; // Lewati jika duplikat
            }

            // Cari kelas berdasarkan nama (jika ada)
            $kelas_id = null;
            if ($kelas_nama) {
                $kelas = Kelas::where('nama_kelas', trim($kelas_nama))->first();
                if ($kelas) {
                    $kelas_id = $kelas->id;
                }
            }

            // Buat user baru
            $user = User::create([
                'name' => $name,
                'username' => $username,
                'password' => Hash::make($password),
                'role' => 'anggota',
            ]);

            // Buat data anggota
            Anggota::create([
                'user_id' => $user->id,
                'nisn' => $username,
                'no_telp' => null,
                'email' => null,
                'kelas_id' => $kelas_id,
            ]);

            $jumlahBerhasil++;
        }

        // Kirim feedback ke user
        return redirect()->back()->with('success', "Import selesai. Berhasil ditambahkan: $jumlahBerhasil, Duplikat dilewati: $jumlahDuplikat.");
    }

    public function setAlumniBulk(Request $request)
    {
        $userIds = $request->input('user_ids', []);

        if (empty($userIds)) {
            return redirect()->back()->with('error', 'Tidak ada pengguna yang dipilih.');
        }

        $users = User::whereIn('id', $userIds)->get();
        $updatedCount = 0;

        foreach ($users as $user) {
            if ($user->anggota) {
                $user->anggota->update(['status' => 'alumni']);
                $updatedCount++;
            } else if ($user->role === 'anggota') {
                Anggota::create([
                    'user_id' => $user->id,
                    'nisn' => $user->username,
                    'no_telp' => null,
                    'email' => null,
                    'kelas_id' => null,
                    'status' => 'alumni',
                ]);
                $updatedCount++;
            }
        }

        if ($updatedCount > 0) {
            return redirect()->back()->with('success', "$updatedCount pengguna berhasil diubah menjadi alumni.");
        }

        return redirect()->back()->with('error', 'Tidak ada pengguna berstatus anggota yang diubah.');
    }

    public function importAlumni(Request $request)
    {
        $request->validate([
            'file_alumni' => 'required|file|mimes:xlsx,xls',
        ], [
            'file_alumni.required' => 'File Excel alumni wajib diupload.',
            'file_alumni.mimes' => 'Format file harus berupa .xlsx atau .xls',
        ]);

        try {
            $file = $request->file('file_alumni');
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            $jumlahBerhasil = 0;
            $jumlahDilewati = 0;
            $tidakDitemukan = [];

            foreach (array_slice($rows, 1) as $row) {
                $namaInput = trim($row[0] ?? '');
                $nisnInput = trim($row[1] ?? '');

                if (!$namaInput && !$nisnInput) {
                    continue;
                }

                $user = null;

                // 1. Cari berdasarkan NISN/Username jika ada
                if ($nisnInput) {
                    $user = User::where('username', $nisnInput)
                        ->orWhereHas('anggota', function ($q) use ($nisnInput) {
                            $q->where('nisn', $nisnInput);
                        })->first();
                }

                // 2. Jika belum ketemu, cari berdasarkan Nama (Case-insensitive)
                if (!$user && $namaInput) {
                    $user = User::whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower($namaInput)])->first();

                    if (!$user) {
                        $user = User::where('name', 'like', "%{$namaInput}%")->first();
                    }
                }

                if ($user) {
                    if ($user->anggota) {
                        $user->anggota->update(['status' => 'alumni']);
                    } else {
                        Anggota::create([
                            'user_id' => $user->id,
                            'nisn' => $user->username,
                            'no_telp' => null,
                            'email' => null,
                            'kelas_id' => null,
                            'status' => 'alumni',
                        ]);
                    }
                    $jumlahBerhasil++;
                } else {
                    $jumlahDilewati++;
                    $tidakDitemukan[] = $namaInput ?: $nisnInput;
                }
            }

            if ($jumlahBerhasil > 0) {
                $msg = "Import Alumni Selesai. Berhasil mengubah $jumlahBerhasil siswa menjadi Alumni.";
                if ($jumlahDilewati > 0) {
                    $daftarNama = implode(', ', array_slice($tidakDitemukan, 0, 5));
                    if (count($tidakDitemukan) > 5) {
                        $daftarNama .= ' dan ' . (count($tidakDitemukan) - 5) . ' nama lainnya';
                    }
                    $msg .= " ($jumlahDilewati data tidak ditemukan di sistem: $daftarNama).";
                }
                return redirect()->back()->with('success', $msg);
            } else {
                return redirect()->back()->with('error', "Gagal memproses Alumni. Tidak ada nama yang cocok di sistem (" . count($tidakDitemukan) . " data tidak ditemukan).");
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses file: ' . $e->getMessage());
        }
    }

    public function downloadTemplateAlumni()
    {
        $filePath = public_path('template_alumni_bulk.xlsx');

        if (file_exists($filePath)) {
            return response()->download($filePath, 'template_alumni_bulk.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        }

        // Fallback: Jika file fisik belum ada di server, buat langsung di memory & stream
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'NISN');
        $sheet->setCellValue('B1', 'Nama');
        $sheet->setCellValue('C1', 'Kelas');
        $sheet->setCellValue('D1', 'Keterangan');

        // Style Header
        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E40AF'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Sample Data
        $sheet->setCellValue('A2', '1234567890');
        $sheet->setCellValue('B2', 'Ahmad Fauzi');
        $sheet->setCellValue('C2', 'XII IPA 1');
        $sheet->setCellValue('D2', 'Lulus');

        $sheet->setCellValue('A3', '0987654321');
        $sheet->setCellValue('B3', 'Siti Aminah');
        $sheet->setCellValue('C3', 'XII IPS 2');
        $sheet->setCellValue('D3', 'Lulus');

        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'template_alumni_bulk.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Import bulk data guru dari Excel.
     * Kolom: A Nama | B Jenis Identitas (NIP/NIK) | C Nomor NIP/NIK | D Password (opsional) | E No. Telp | F Email
     */
    public function importGuru(Request $request)
    {
        $request->validate([
            'file_guru' => 'required|file|mimes:xlsx,xls,csv',
        ], [
            'file_guru.required' => 'File Excel data guru wajib diupload.',
            'file_guru.mimes' => 'Format file harus berupa .xlsx, .xls atau .csv',
        ]);

        try {
            $spreadsheet = IOFactory::load($request->file('file_guru')->getPathname());
            $rows = $spreadsheet->getActiveSheet()->toArray(null, true, false, false);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'File tidak dapat dibaca: ' . $e->getMessage());
        }

        $berhasil = 0;
        $duplikat = 0;
        $gagal = [];

        foreach (array_slice($rows, 1) as $i => $row) {
            $barisKe = $i + 2;
            $nama = trim((string) ($row[0] ?? ''));
            $jenis = strtoupper(trim((string) ($row[1] ?? '')));
            // Ambil digit saja (spasi/titik/kutip yang ikut tersalin dari Excel dibuang)
            $nomor = preg_replace('/[^0-9]/', '', (string) ($row[2] ?? ''));
            $password = trim((string) ($row[3] ?? ''));
            $noTelp = trim((string) ($row[4] ?? '')) ?: null;
            $email = trim((string) ($row[5] ?? '')) ?: null;

            if ($nama === '' && $nomor === '') {
                continue; // baris kosong
            }

            if ($nama === '' || $nomor === '') {
                $gagal[] = "baris $barisKe (nama/NIP-NIK kosong)";
                continue;
            }

            if (!in_array($jenis, ['NIP', 'NIK'])) {
                // Tebak otomatis: NIK 16 digit, selain itu dianggap NIP (NIP PNS 18 digit)
                $jenis = strlen($nomor) === 16 ? 'NIK' : 'NIP';
            }

            if (User::where('username', $nomor)->exists() || Anggota::where('nisn', $nomor)->exists()) {
                $duplikat++;
                continue;
            }

            if ($email && (!filter_var($email, FILTER_VALIDATE_EMAIL) || Anggota::where('email', $email)->exists())) {
                $email = null; // abaikan email tidak valid / sudah dipakai
            }

            try {
                DB::transaction(function () use ($nama, $nomor, $password, $jenis, $noTelp, $email) {
                    $user = User::create([
                        'name' => $nama,
                        'username' => $nomor,
                        // Password default = NIP/NIK jika kolom password kosong / terlalu pendek
                        'password' => Hash::make(strlen($password) >= 6 ? $password : $nomor),
                        'role' => 'anggota',
                    ]);

                    $this->buatAnggotaGuru($user, $nomor, $jenis, $noTelp, $email);
                });
                $berhasil++;
            } catch (\Throwable $e) {
                $gagal[] = "baris $barisKe ($nama)";
            }
        }

        $pesan = "Import guru selesai. Berhasil ditambahkan: $berhasil";
        if ($duplikat) {
            $pesan .= ", NIP/NIK sudah terdaftar (dilewati): $duplikat";
        }
        if (count($gagal)) {
            $pesan .= '. Gagal: ' . implode(', ', array_slice($gagal, 0, 5))
                . (count($gagal) > 5 ? ' dan ' . (count($gagal) - 5) . ' lainnya' : '');
        }

        return redirect()->route('admin.pengguna.index', ['category' => 'guru'])
            ->with($berhasil > 0 ? 'success' : 'error', $pesan . '.');
    }

    public function downloadTemplateGuru()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Guru');

        $sheet->fromArray([
            'Nama Lengkap',
            'Jenis Identitas (NIP/NIK)',
            'Nomor NIP / NIK',
            'Password (opsional)',
            'No. Telp (opsional)',
            'Email (opsional)',
        ], null, 'A1');

        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E40AF'],
            ],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);

        // Contoh data — ditulis sebagai teks agar NIP/NIK tidak berubah jadi notasi ilmiah
        $contoh = [
            ['Drs. Ahmad Fauzi, M.Pd', 'NIP', '196805121994031005', '', '081234567890', 'ahmad.fauzi@example.com'],
            ['Siti Aminah, S.Pd', 'NIK', '3604015506900002', 'rahasia123', '', ''],
        ];
        foreach ($contoh as $r => $data) {
            foreach ($data as $c => $val) {
                $sheet->setCellValueExplicit([$c + 1, $r + 2], $val, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            }
        }
        $sheet->getStyle('C2:C1000')->getNumberFormat()->setFormatCode('@');

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->setCellValue('H1', 'Petunjuk Pengisian:');
        $sheet->setCellValue('H2', '1. Kolom Nama Lengkap & Nomor NIP/NIK wajib diisi.');
        $sheet->setCellValue('H3', '2. Jenis Identitas diisi NIP atau NIK (jika kosong: 16 digit = NIK, selain itu NIP).');
        $sheet->setCellValue('H4', '3. Username login guru = Nomor NIP/NIK.');
        $sheet->setCellValue('H5', '4. Jika Password kosong, password default = Nomor NIP/NIK.');
        $sheet->setCellValue('H6', '5. Hapus baris contoh sebelum mengupload.');
        $sheet->getStyle('H1')->getFont()->setBold(true);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'template_import_guru.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}

