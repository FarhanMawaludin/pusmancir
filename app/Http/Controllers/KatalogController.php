<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Katalog;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;


class KatalogController extends Controller
{
    public function index(Request $request)
    {
        $activeMenu = 'katalog';

        $search = $request->input('search');
        $category = $request->input('category', 'judul_buku'); // default 'judul_buku'

        $query = Katalog::with('inventori')->orderBy('created_at', 'asc');

        if ($search && $category !== 'all') {
            $query->where(function ($q) use ($search, $category) {
                switch ($category) {
                    case 'judul_buku':
                        $q->where('judul_buku', 'like', "%{$search}%");
                        break;
                    case 'penerbit':
                        $q->where('penerbit', 'like', "%{$search}%");
                        break;
                    case 'pengarang':
                        $q->where('pengarang', 'like', "%{$search}%");
                        break;
                    case 'kategori':
                        $q->where('kategori', 'like', "%{$search}%");
                        break;
                    case 'isbn':
                        $q->where('isbn', 'like', "%{$search}%");
                        break;
                }
            });
        }

        $katalog = $query->paginate(10)->appends([
            'search' => $search,
            'category' => $category
        ]);

        return view('admin.katalog.index', compact('activeMenu', 'katalog', 'search', 'category'));
    }



    public function edit($id)
    {
        $katalog = Katalog::with('inventori')->findOrFail($id);
        $activeMenu = "katalog";

        return view('admin.katalog.edit', compact('katalog', 'activeMenu'));
    }

    /**
     * Simpan perubahan data katalog.
     */
    // public function update(Request $request, $id)
    // {
    //     $katalog = Katalog::findOrFail($id);

    //     $validated = $request->validate([
    //         'isbn'            => 'nullable|string|max:255',
    //         'ringkasan_buku'  => 'nullable|string',
    //         'kode_ddc'        => 'nullable|string|max:100',
    //         'no_panggil'      => 'nullable|string|max:100',
    //         'cover_buku'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    //         'cover_buku_url' => 'nullable|string|max:255',
    //     ]);

    //     // Handle upload cover jika diunggah
    //     if ($request->hasFile('cover_buku')) {
    //         // Hapus file lama jika ada
    //         if ($katalog->cover_buku && Storage::exists('public/' . $katalog->cover_buku)) {
    //             Storage::delete('public/' . $katalog->cover_buku);
    //         }

    //         // Simpan file baru yang diupload user
    //         $validated['cover_buku'] = $request->file('cover_buku')->store('cover_buku', 'public');
    //     } elseif ($request->filled('cover_buku_url')) {
    //         // Jika tidak upload, gunakan path dari hidden input
    //         $validated['cover_buku'] = $request->cover_buku_url;
    //     }

    //     // Update data katalog
    //     $katalog->update($validated);

    //     return redirect()->route('admin.katalog.index')
    //         ->with('success', 'Data katalog berhasil diperbarui.');
    // }


    public function update(Request $request, $id)
    {
        $katalog = Katalog::findOrFail($id);

        $validated = $request->validate([
            'isbn'            => 'nullable|string|max:255',
            'ringkasan_buku'  => 'nullable|string',
            'kode_ddc'        => 'nullable|string|max:100',
            'no_panggil'      => 'nullable|string|max:100',
            'cover_buku'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'cover_buku_url'  => 'nullable|string|max:255',
        ]);

        // Handle upload cover jika diunggah
        if ($request->hasFile('cover_buku')) {
            // Hapus file lama jika ada
            if ($katalog->cover_buku && file_exists(public_path($katalog->cover_buku))) {
                unlink(public_path($katalog->cover_buku));
            }

            // Simpan file baru ke public/cover_buku
            $file = $request->file('cover_buku');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('cover_buku'), $filename);

            $validated['cover_buku'] = 'cover_buku/' . $filename;
        } elseif ($request->filled('cover_buku_url')) {
            // Jika tidak upload, gunakan path dari input hidden
            $validated['cover_buku'] = $request->cover_buku_url;
        }

        // Update data katalog
        $katalog->update($validated);

        return redirect()->route('admin.katalog.index')
            ->with('success', 'Data katalog berhasil diperbarui.');
    }



    public function generateRingkasan(Request $request)
    {
        $judul = $request->input('judul');
        $pengarang = $request->input('pengarang');

        $prompt = "Tuliskan sinopsis singkat dan langsung ke inti cerita dari buku berjudul \"$judul\" karya \"$pengarang\". 
                    Hindari penjelasan tambahan seperti kata 'Sinopsis', 'Inti Cerita', atau heading lainnya. Langsung tuliskan ringkasannya saja dalam paragraf yang rapi dan natural.";

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . config('services.openrouter.api_key'),
            ])->post('https://openrouter.ai/api/v1/chat/completions', [
                'model' => 'google/gemma-3n-e2b-it:free',
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ]
            ]);

            $data = $response->json();

            // Log hanya jika $data terisi
            logger()->info('OpenRouter Response:', is_array($data) ? $data : ['response' => $response->body()]);

            $text = $data['choices'][0]['message']['content'] ?? 'Ringkasan tidak tersedia.';

            return response()->json([
                'success' => true,
                'ringkasan' => trim($text)
            ]);
        } catch (\Exception $e) {
            logger()->error('OpenRouter Error:', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'error' => 'Terjadi kesalahan saat memanggil API.'
            ]);
        }
    }

    public function generateKodeDDC(Request $request)
    {
        $judul = $request->input('judul');
        $pengarang = $request->input('pengarang');

        $prompt = "Tentukan Kode DDC dan Nomor Panggil untuk buku berjudul \"$judul\" karya \"$pengarang\". 
Nomor Panggil harus mengikuti standar penulisan perpustakaan: dimulai dengan kode DDC, diikuti garis miring (/), lalu tiga huruf pertama dari nama belakang pengarang, semua tanpa penjelasan tambahan. 
Tampilkan hasil akhir hanya seperti ini:

Kode DDC: [kode]
Nomor Panggil: [kode_ddc]/[3huruf_nama_belakang_pengarang]";

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . config('services.openrouter.api_key'),
            ])->post('https://openrouter.ai/api/v1/chat/completions', [
                'model' => 'google/gemma-3n-e2b-it:free',
                'temperature' => 0.3,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ]
            ]);

            if (!$response->successful()) {
                $status = $response->status();
                $body = $response->json();

                if ($status === 429) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Limit penggunaan model gratis sudah habis hari ini. Silakan coba lagi besok atau tambahkan kredit di akun OpenRouter.'
                    ]);
                }

                logger()->error('Gagal panggil API OpenRouter', [
                    'status' => $status,
                    'body' => $body
                ]);

                return response()->json([
                    'success' => false,
                    'error' => 'API gagal merespon dengan benar.'
                ]);
            }


            $data = $response->json();
            logger()->info('OpenRouter Response DDC:', is_array($data) ? $data : ['response' => $response->body()]);

            $text = $data['choices'][0]['message']['content'] ?? null;

            if (!$text) {
                return response()->json([
                    'success' => false,
                    'error' => 'Jawaban tidak tersedia dari AI.'
                ]);
            }

            // Regex yang lebih fleksibel
            preg_match('/Kode DDC\s*:\s*(.+)/i', $text, $ddcMatch);
            preg_match('/Nomor Panggil\s*:\s*(.+)/i', $text, $panggilMatch);

            $kode_ddc = trim($ddcMatch[1] ?? '');
            $no_panggil = trim($panggilMatch[1] ?? '');

            if (!$kode_ddc || !$no_panggil) {
                return response()->json([
                    'success' => false,
                    'error' => 'Format jawaban AI tidak sesuai.'
                ]);
            }

            return response()->json([
                'success' => true,
                'kode_ddc' => $kode_ddc,
                'no_panggil' => $no_panggil
            ]);
        } catch (\Exception $e) {
            logger()->error('OpenRouter Error DDC:', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'error' => 'Terjadi kesalahan saat memanggil API.'
            ]);
        }
    }



    public function generateISBN(Request $request)
    {
        $judul = $request->input('judul');
        $pengarang = $request->input('pengarang');

        $prompt = "Berikan satu nomor ISBN (ISBN-10 atau ISBN-13) yang valid dan pasti tersedia di database Google Books untuk buku berjudul \"$judul\" karya \"$pengarang\". 
Tulis hanya nomor ISBN tersebut, tanpa penjelasan, tanpa teks tambahan, dan hanya menggunakan angka serta tanda hubung. 
Pastikan ISBN tersebut benar-benar ada dan dapat digunakan untuk mencari cover buku di Google Books API.";

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . config('services.openrouter.api_key'),
            ])->post('https://openrouter.ai/api/v1/chat/completions', [
                'model' => 'google/gemma-3n-e2b-it:free',
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ]
            ]);

            $data = $response->json();

            // Log responsenya
            logger()->info('OpenRouter ISBN Response:', is_array($data) ? $data : ['response' => $response->body()]);

            $isbn = $data['choices'][0]['message']['content'] ?? 'ISBN tidak tersedia.';

            return response()->json([
                'success' => true,
                'isbn' => trim($isbn)
            ]);
        } catch (\Exception $e) {
            logger()->error('OpenRouter ISBN Error:', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'error' => 'Terjadi kesalahan saat memanggil API.'
            ]);
        }
    }





    // public function fetchCoverByIsbn($isbn)
    // {
    //     try {
    //         // 1. Coba ambil dari Google Books
    //         $res = Http::get("https://www.googleapis.com/books/v1/volumes?q=isbn:$isbn");

    //         $thumbnail = null;

    //         if ($res->ok() && isset($res['items'][0]['volumeInfo']['imageLinks'])) {
    //             $links = $res['items'][0]['volumeInfo']['imageLinks'];
    //             $thumbnail = $links['large'] ??
    //                 $links['medium'] ??
    //                 $links['small'] ??
    //                 $links['thumbnail'] ??
    //                 $links['smallThumbnail'] ?? null;
    //         }

    //         // 2. Jika Google Books tidak punya, coba dari Open Library
    //         if (!$thumbnail) {
    //             $openLibUrl = "https://covers.openlibrary.org/b/isbn/{$isbn}-L.jpg"; // L = large size
    //             // Cek apakah gambarnya valid
    //             $checkImage = @getimagesize($openLibUrl);
    //             if ($checkImage !== false) {
    //                 $thumbnail = $openLibUrl;
    //             }
    //         }

    //         // 3. Jika tetap tidak ada cover
    //         if (!$thumbnail) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Cover tidak tersedia untuk ISBN ini.'
    //             ]);
    //         }

    //         // 4. Unduh & simpan gambar
    //         $thumbnail = str_replace('http://', 'https://', $thumbnail);
    //         $imageContent = file_get_contents($thumbnail);

    //         if (!$imageContent) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Gagal mengunduh gambar dari URL.'
    //             ]);
    //         }

    //         $filename = 'cover_' . $isbn . '_' . Str::random(5) . '.jpg';
    //         $path = 'cover_buku/' . $filename;
    //         Storage::disk('public')->put($path, $imageContent);

    //         return response()->json([
    //             'success' => true,
    //             'cover_url' => asset('storage/' . $path),
    //             'path' => $path
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Terjadi kesalahan: ' . $e->getMessage()
    //         ]);
    //     }
    // }

    public function fetchCoverByIsbn($isbn)
    {
        try {
            // 1. Coba ambil dari Google Books
            $res = Http::get("https://www.googleapis.com/books/v1/volumes?q=isbn:$isbn");

            $thumbnail = null;

            if ($res->ok() && isset($res['items'][0]['volumeInfo']['imageLinks'])) {
                $links = $res['items'][0]['volumeInfo']['imageLinks'];
                $thumbnail = $links['large'] ??
                    $links['medium'] ??
                    $links['small'] ??
                    $links['thumbnail'] ??
                    $links['smallThumbnail'] ?? null;
            }

            // 2. Jika Google Books tidak punya, coba dari Open Library
            if (!$thumbnail) {
                $openLibUrl = "https://covers.openlibrary.org/b/isbn/{$isbn}-L.jpg"; // L = large size
                // Cek apakah gambarnya valid
                $checkImage = @getimagesize($openLibUrl);
                if ($checkImage !== false) {
                    $thumbnail = $openLibUrl;
                }
            }

            // 3. Jika tetap tidak ada cover
            if (!$thumbnail) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cover tidak tersedia untuk ISBN ini.'
                ]);
            }

            // 4. Unduh & simpan gambar langsung ke public/cover_buku
            $thumbnail = str_replace('http://', 'https://', $thumbnail);
            $imageContent = file_get_contents($thumbnail);

            if (!$imageContent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengunduh gambar dari URL.'
                ]);
            }

            $filename = 'cover_' . $isbn . '_' . Str::random(5) . '.jpg';
            $folderPath = public_path('cover_buku');

            // Pastikan folder ada
            if (!file_exists($folderPath)) {
                mkdir($folderPath, 0755, true);
            }

            $filePath = $folderPath . DIRECTORY_SEPARATOR . $filename;

            // Simpan file
            file_put_contents($filePath, $imageContent);

            return response()->json([
                'success' => true,
                'cover_url' => asset('cover_buku/' . $filename),
                'path' => 'cover_buku/' . $filename
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
}
