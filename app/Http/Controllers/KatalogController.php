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
        $sort = $request->input('sort', 'desc'); // default to 'desc' (terakhir diinput)

        $query = Katalog::with('inventori')->orderBy('created_at', $sort);

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
            'category' => $category,
            'sort' => $sort
        ]);

        return view('admin.katalog.index', compact('activeMenu', 'katalog', 'search', 'category', 'sort'));
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

        return redirect()->route('admin.katalog.index', [
            'page' => $request->input('page'),
            'sort' => $request->input('sort'),
            'search' => $request->input('search'),
            'category' => $request->input('category'),
        ])->with('success', 'Data katalog berhasil diperbarui.');
    }



    private function askAI($prompt, $temperature = null)
    {
        $geminiApiKey = env('GEMINI_API_KEY');
        $grokApiKey = env('GROK_API_KEY') ?: env('XAI_API_KEY');
        $openrouterApiKey = env('OPENROUTER_API_KEY') ?: config('services.openrouter.api_key');

        if (!$geminiApiKey && !$grokApiKey && !$openrouterApiKey) {
            return [
                'success' => false,
                'error' => 'API Key belum dikonfigurasi. Silakan tambahkan GEMINI_API_KEY di file .env Anda.'
            ];
        }

        // 1. Prioritas Utama: Google Gemini
        if ($geminiApiKey) {
            try {
                $model = env('GEMINI_MODEL', 'gemini-1.5-flash');
                
                $payload = [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ]
                ];

                if ($temperature !== null) {
                    $payload['generationConfig'] = [
                        'temperature' => $temperature
                    ];
                }

                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'x-goog-api-key' => $geminiApiKey,
                ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", $payload);

                if (!$response->successful()) {
                    $status = $response->status();
                    $errorDetails = 'API Gemini gagal merespon dengan benar.';
                    try {
                        $body = $response->json();
                        if (isset($body['error']['message'])) {
                            $errorDetails = $body['error']['message'];
                        }
                    } catch (\Exception $e) {
                        $errorDetails = $response->body() ?: 'Gagal merespon';
                    }

                    logger()->error('Gagal panggil API Gemini', ['status' => $status, 'body' => $response->body()]);
                    return [
                        'success' => false,
                        'error' => $errorDetails . ' (Status: ' . $status . ')'
                    ];
                }

                $data = $response->json();
                $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

                if (!$text) {
                    return [
                        'success' => false,
                        'error' => 'Respon tidak tersedia dari Gemini API.'
                    ];
                }

                return [
                    'success' => true,
                    'text' => trim($text)
                ];
            } catch (\Exception $e) {
                logger()->error('Gemini API Error:', ['error' => $e->getMessage()]);
                return [
                    'success' => false,
                    'error' => 'Terjadi kesalahan saat memanggil Gemini API: ' . $e->getMessage()
                ];
            }
        }

        // 2. Prioritas Kedua: OpenRouter
        if ($openrouterApiKey) {
            try {
                $model = env('OPENROUTER_MODEL', 'google/gemma-4-31b-it:free');
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $openrouterApiKey,
                ])->post('https://openrouter.ai/api/v1/chat/completions', [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'temperature' => $temperature !== null ? $temperature : 1.0
                ]);

                if (!$response->successful()) {
                    $status = $response->status();
                    
                    if ($status === 429) {
                        return [
                            'success' => false,
                            'error' => 'Limit penggunaan model gratis OpenRouter sudah habis. Silakan coba lagi nanti atau gunakan direct Gemini API.'
                        ];
                    }

                    $errorDetails = 'API OpenRouter gagal merespon dengan benar.';
                    try {
                        $body = $response->json();
                        if (isset($body['error']['message'])) {
                            $errorDetails = $body['error']['message'];
                        }
                    } catch (\Exception $e) {
                        $errorDetails = $response->body() ?: 'Gagal merespon';
                    }

                    logger()->error('Gagal panggil API OpenRouter', ['status' => $status, 'body' => $response->body()]);
                    return [
                        'success' => false,
                        'error' => $errorDetails . ' (Status: ' . $status . ')'
                    ];
                }

                $data = $response->json();
                $text = $data['choices'][0]['message']['content'] ?? null;

                if (!$text) {
                    return [
                        'success' => false,
                        'error' => 'Respon tidak tersedia dari OpenRouter.'
                    ];
                }

                return [
                    'success' => true,
                    'text' => trim($text)
                ];
            } catch (\Exception $e) {
                logger()->error('OpenRouter Error:', ['error' => $e->getMessage()]);
                return [
                    'success' => false,
                    'error' => 'Terjadi kesalahan saat memanggil OpenRouter: ' . $e->getMessage()
                ];
            }
        }

        // 3. Prioritas Ketiga: xAI Grok
        if ($grokApiKey) {
            try {
                $model = env('GROK_MODEL', 'grok-2-1212');
                
                $payload = [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt]
                    ]
                ];

                if ($temperature !== null) {
                    $payload['temperature'] = $temperature;
                }

                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $grokApiKey,
                ])->post('https://api.x.ai/v1/chat/completions', $payload);

                if (!$response->successful()) {
                    $status = $response->status();
                    $errorDetails = 'API Grok gagal merespon dengan benar.';
                    try {
                        $body = $response->json();
                        if (isset($body['error']['message'])) {
                            $errorDetails = $body['error']['message'];
                        }
                    } catch (\Exception $e) {
                        $errorDetails = $response->body() ?: 'Gagal merespon';
                    }

                    logger()->error('Gagal panggil API Grok', ['status' => $status, 'body' => $response->body()]);
                    return [
                        'success' => false,
                        'error' => $errorDetails . ' (Status: ' . $status . ')'
                    ];
                }

                $data = $response->json();
                $text = $data['choices'][0]['message']['content'] ?? null;

                if (!$text) {
                    return [
                        'success' => false,
                        'error' => 'Respon tidak tersedia dari Grok API.'
                    ];
                }

                return [
                    'success' => true,
                    'text' => trim($text)
                ];
            } catch (\Exception $e) {
                logger()->error('Grok API Error:', ['error' => $e->getMessage()]);
                return [
                    'success' => false,
                    'error' => 'Terjadi kesalahan saat memanggil Grok API: ' . $e->getMessage()
                ];
            }
        }
    }

    public function generateRingkasan(Request $request)
    {
        $judul = $request->input('judul');
        $pengarang = $request->input('pengarang');

        $prompt = "Tuliskan sinopsis singkat dan langsung ke inti cerita dari buku berjudul \"$judul\" karya \"$pengarang\". 
                    Hindari penjelasan tambahan seperti kata 'Sinopsis', 'Inti Cerita', atau heading lainnya. Langsung tuliskan ringkasannya saja dalam paragraf yang rapi dan natural.";

        $aiResult = $this->askAI($prompt);

        if (!$aiResult['success']) {
            return response()->json([
                'success' => false,
                'error' => $aiResult['error']
            ]);
        }

        return response()->json([
            'success' => true,
            'ringkasan' => $aiResult['text']
        ]);
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

        $aiResult = $this->askAI($prompt, 0.3);

        if (!$aiResult['success']) {
            return response()->json([
                'success' => false,
                'error' => $aiResult['error']
            ]);
        }

        $text = $aiResult['text'];

        // Regex yang lebih fleksibel
        preg_match('/Kode DDC\s*:\s*(.+)/i', $text, $ddcMatch);
        preg_match('/Nomor Panggil\s*:\s*(.+)/i', $text, $panggilMatch);

        $kode_ddc = trim($ddcMatch[1] ?? '');
        $no_panggil = trim($panggilMatch[1] ?? '');

        if (!$kode_ddc || !$no_panggil) {
            return response()->json([
                'success' => false,
                'error' => 'Format jawaban AI tidak sesuai. Respon AI: ' . substr($text, 0, 100) . '...'
            ]);
        }

        return response()->json([
            'success' => true,
            'kode_ddc' => $kode_ddc,
            'no_panggil' => $no_panggil
        ]);
    }

    public function generateISBN(Request $request)
    {
        $judul = $request->input('judul');
        $pengarang = $request->input('pengarang');

        $prompt = "Berikan satu nomor ISBN (ISBN-10 atau ISBN-13) yang valid dan pasti tersedia di database Google Books untuk buku berjudul \"$judul\" karya \"$pengarang\". 
Tulis hanya nomor ISBN tersebut, tanpa penjelasan, tanpa teks tambahan, dan hanya menggunakan angka serta tanda hubung. 
Pastikan ISBN tersebut benar-benar ada dan dapat digunakan untuk mencari cover buku di Google Books API.";

        $aiResult = $this->askAI($prompt);

        if (!$aiResult['success']) {
            return response()->json([
                'success' => false,
                'error' => $aiResult['error']
            ]);
        }

        return response()->json([
            'success' => true,
            'isbn' => $aiResult['text']
        ]);
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
