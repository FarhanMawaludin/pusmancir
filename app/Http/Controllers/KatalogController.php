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



    private function askAI($prompt, $temperature = null, $provider = null)
    {
        $geminiApiKey = env('GEMINI_API_KEY');
        $grokApiKey = env('GROK_API_KEY') ?: env('XAI_API_KEY');
        $openrouterApiKey = env('OPENROUTER_API_KEY') ?: config('services.openrouter.api_key');

        if (!$geminiApiKey && !$grokApiKey && !$openrouterApiKey) {
            return [
                'success' => false,
                'error' => 'API Key belum dikonfigurasi. Silakan tambahkan API key di file .env Anda.'
            ];
        }

        // ====================================================================
        // FORCED PROVIDER: OPENROUTER
        // ====================================================================
        if ($provider === 'openrouter') {
            if (!$openrouterApiKey) {
                return [
                    'success' => false,
                    'error' => 'API Key OpenRouter belum dikonfigurasi di file .env.'
                ];
            }
            return $this->executeOpenRouter($prompt, $temperature, $openrouterApiKey);
        }

        // ====================================================================
        // FORCED PROVIDER: GEMINI (Default / Fallback)
        // ====================================================================
        if ($geminiApiKey) {
            $geminiRes = $this->executeGemini($prompt, $temperature, $geminiApiKey);
            if ($geminiRes['success']) {
                return $geminiRes;
            }
            
            // Jika Gemini gagal, dan OpenRouter tersedia, kita otomatis coba fallback ke OpenRouter
            if ($openrouterApiKey) {
                logger()->info('Gemini gagal, mencoba fallback ke OpenRouter...');
                return $this->executeOpenRouter($prompt, $temperature, $openrouterApiKey);
            }
            
            return $geminiRes;
        }

        // Fallback jika Gemini API Key tidak ada tetapi OpenRouter ada
        if ($openrouterApiKey) {
            return $this->executeOpenRouter($prompt, $temperature, $openrouterApiKey);
        }

        // Fallback jika hanya Grok yang ada
        if ($grokApiKey) {
            return $this->executeGrok($prompt, $temperature, $grokApiKey);
        }

        return [
            'success' => false,
            'error' => 'Tidak ada penyedia AI yang valid dikonfigurasi.'
        ];
    }

    private function executeGemini($prompt, $temperature, $apiKey)
    {
        try {
            $model = env('GEMINI_MODEL', 'gemini-2.5-flash');
            
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
                'x-goog-api-key' => $apiKey,
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

    private function executeOpenRouter($prompt, $temperature, $apiKey)
    {
        try {
            $model = env('OPENROUTER_MODEL', 'meta-llama/llama-3.1-8b-instruct:free');
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $apiKey,
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

    private function executeGrok($prompt, $temperature, $apiKey)
    {
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
                'Authorization' => 'Bearer ' . $apiKey,
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

    public function generateRingkasan(Request $request)
    {
        $judul = $request->input('judul');
        $pengarang = $request->input('pengarang');
        $provider = $request->input('provider'); // 'gemini' atau 'openrouter'

        $prompt = "Tuliskan sinopsis singkat dan langsung ke inti cerita dari buku berjudul \"$judul\" karya \"$pengarang\". 
                    Hindari penjelasan tambahan seperti kata 'Sinopsis', 'Inti Cerita', atau heading lainnya. Langsung tuliskan ringkasannya saja dalam paragraf yang rapi dan natural.";

        $aiResult = $this->askAI($prompt, null, $provider);

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
        $provider = $request->input('provider'); // 'gemini' atau 'openrouter'

        $prompt = "Tentukan Kode DDC dan Nomor Panggil untuk buku berjudul \"$judul\" karya \"$pengarang\". 
Format Nomor Panggil harus mengikuti aturan penulisan berikut:
[Kode DDC] [Tiga huruf pertama nama belakang pengarang, harus HURUF KAPITAL] [Satu huruf pertama dari judul buku, harus HURUF KECIL].
Semba bagian dipisahkan oleh spasi (tanpa menggunakan garis miring).
Contoh penulisan: 302.224 NUR d

Tampilkan hasil akhir hanya seperti ini:

Kode DDC: [kode]
Nomor Panggil: [nomor_panggil]";

        $aiResult = $this->askAI($prompt, 0.3, $provider);

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
        $judul    = $request->input('judul');
        $pengarang = $request->input('pengarang');
        $penerbit  = $request->input('penerbit', '');
        $provider  = $request->input('provider'); // 'gemini' atau 'openrouter'

        // ====================================================================
        // LAYER 1: Gramedia.com Web Scraping (prioritas utama — buku Indonesia)
        // Strategi: ambil SKU dari __NEXT_DATA__, lalu panggil Gramedia variant
        // API untuk mendapatkan ISBN resmi.
        // ====================================================================
        try {
            $slug = Str::slug($judul);

            $res = Http::timeout(10)->withHeaders([
                'User-Agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language' => 'id-ID,id;q=0.9,en;q=0.8',
            ])->get("https://www.gramedia.com/products/{$slug}");

            if ($res->ok()) {
                $html = $res->body();

                // ── Strategi A: Ekstrak dari __NEXT_DATA__ JSON (Next.js SSR) ──
                // Gramedia menyimpan data produk dalam tag <script id="__NEXT_DATA__">
                // Di dalamnya ada field "sku" yang bisa dipakai untuk call variant API
                if (preg_match('/<script id="__NEXT_DATA__"[^>]*>(.+?)<\/script>/s', $html, $scriptMatch)) {
                    $nextData = json_decode($scriptMatch[1], true);

                    // Cek isbn langsung di productDetailMeta (kadang sudah terisi)
                    $isbnDirect = $nextData['props']['pageProps']['productDetailMeta']['isbn'] ?? '';
                    if (!empty($isbnDirect) && preg_match('/^97[89]\d{10}$/', preg_replace('/[^0-9]/', '', $isbnDirect))) {
                        return response()->json([
                            'success'    => true,
                            'isbn'       => preg_replace('/[^0-9]/', '', $isbnDirect),
                            'source'     => 'Gramedia',
                            'confidence' => 'high',
                        ]);
                    }

                    // Ambil SKU lalu panggil Gramedia variant/product detail API
                    $sku = $nextData['props']['pageProps']['productDetailMeta']['sku'] ?? '';
                    if ($sku) {
                        // Gramedia API endpoint untuk detail produk per SKU
                        $apiRes = Http::timeout(8)->withHeaders([
                            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                            'Accept'     => 'application/json',
                            'Referer'    => "https://www.gramedia.com/products/{$slug}",
                        ])->get("https://api-service.gramedia.com/api/v1/products/{$sku}");

                        if ($apiRes->ok()) {
                            $apiData = $apiRes->json();
                            // ISBN biasanya ada di data.isbn atau data.variants[].isbn
                            $isbnApi = $apiData['data']['isbn']
                                ?? $apiData['data']['variants'][0]['isbn']
                                ?? $apiData['isbn']
                                ?? '';
                            if (!empty($isbnApi)) {
                                $cleanIsbnApi = preg_replace('/[^0-9]/', '', $isbnApi);
                                if (strlen($cleanIsbnApi) >= 10) {
                                    return response()->json([
                                        'success'    => true,
                                        'isbn'       => $cleanIsbnApi,
                                        'source'     => 'Gramedia',
                                        'confidence' => 'high',
                                    ]);
                                }
                            }
                        }
                    }
                }

                // ── Strategi B: Regex langsung di HTML (fallback jika JS sudah di-render) ──
                // Cari pola ISBN-13 di area "Detail Buku"
                if (preg_match('/ISBN\s*[:\s]*\s*(\d{13})/i', $html, $match)) {
                    return response()->json([
                        'success'    => true,
                        'isbn'       => $match[1],
                        'source'     => 'Gramedia',
                        'confidence' => 'high',
                    ]);
                }

                // ── Strategi C: Regex pola angka 978/979 13 digit di seluruh HTML ──
                if (preg_match('/\b(97[89]\d{10})\b/', $html, $match)) {
                    return response()->json([
                        'success'    => true,
                        'isbn'       => $match[1],
                        'source'     => 'Gramedia',
                        'confidence' => 'high',
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::warning('ISBN Lookup - Gramedia scraping gagal: ' . $e->getMessage());
        }

        // ====================================================================
        // LAYER 2: Google Books API
        // ====================================================================
        try {
            $query = urlencode("intitle:{$judul} inauthor:{$pengarang}");
            $res = Http::timeout(10)->get("https://www.googleapis.com/books/v1/volumes?q={$query}&maxResults=5");

            if ($res->ok() && isset($res['items'])) {
                foreach ($res['items'] as $item) {
                    $volumeInfo  = $item['volumeInfo'] ?? [];
                    $identifiers = $volumeInfo['industryIdentifiers'] ?? [];

                    // Cek kecocokan judul (minimal 60% mirip)
                    similar_text(strtolower($volumeInfo['title'] ?? ''), strtolower($judul), $titlePercent);
                    if ($titlePercent < 60) continue;

                    $isbn13 = null;
                    $isbn10 = null;
                    foreach ($identifiers as $id) {
                        if ($id['type'] === 'ISBN_13') $isbn13 = $id['identifier'];
                        elseif ($id['type'] === 'ISBN_10') $isbn10 = $id['identifier'];
                    }

                    $isbn = $isbn13 ?? $isbn10;
                    if ($isbn) {
                        return response()->json([
                            'success'    => true,
                            'isbn'       => $isbn,
                            'source'     => 'Google Books',
                            'confidence' => 'high',
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('ISBN Lookup - Google Books gagal: ' . $e->getMessage());
        }

        // ====================================================================
        // LAYER 3: Open Library API
        // ====================================================================
        try {
            $res = Http::timeout(10)->get('https://openlibrary.org/search.json', [
                'title'  => $judul,
                'author' => $pengarang,
                'limit'  => 5,
                'fields' => 'title,author_name,isbn,publisher',
            ]);

            if ($res->ok()) {
                foreach (($res->json('docs') ?? []) as $doc) {
                    similar_text(strtolower($doc['title'] ?? ''), strtolower($judul), $titlePercent);
                    if ($titlePercent < 60) continue;

                    $isbns = $doc['isbn'] ?? [];
                    $isbn13 = null;
                    $isbn10 = null;
                    foreach ($isbns as $isbn) {
                        $clean = preg_replace('/[^0-9X]/', '', strtoupper($isbn));
                        if (strlen($clean) === 13) { $isbn13 = $clean; break; }
                        elseif (strlen($clean) === 10 && !$isbn10) $isbn10 = $clean;
                    }

                    $isbn = $isbn13 ?? $isbn10;
                    if ($isbn) {
                        return response()->json([
                            'success'    => true,
                            'isbn'       => $isbn,
                            'source'     => 'Open Library',
                            'confidence' => 'high',
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('ISBN Lookup - Open Library gagal: ' . $e->getMessage());
        }

        // ====================================================================
        // LAYER 4: AI Fallback (Gemini/OpenRouter) — terakhir, confidence rendah
        // ====================================================================
        $penerbitInfo = $penerbit ? " diterbitkan oleh \"{$penerbit}\"" : '';
        $prompt = "Berikan satu nomor ISBN (ISBN-10 atau ISBN-13) yang paling sering muncul di internet untuk buku berjudul \"{$judul}\" karya \"{$pengarang}\"{$penerbitInfo}.
Jika kamu tidak yakin dengan ISBN-nya, jawab dengan teks: TIDAK_DITEMUKAN
Jika kamu yakin, tulis HANYA nomor ISBN tersebut saja, tanpa penjelasan, tanpa teks tambahan, hanya angka (contoh: 9786231342355).";

        $aiResult = $this->askAI($prompt, 0.1, $provider);

        if (!$aiResult['success']) {
            return response()->json([
                'success' => false,
                'error'   => 'ISBN tidak ditemukan di semua sumber. AI juga gagal: ' . $aiResult['error'],
            ]);
        }

        $aiIsbn   = trim($aiResult['text']);
        $cleanAI  = preg_replace('/[^0-9]/', '', $aiIsbn);

        if (stripos($aiIsbn, 'TIDAK_DITEMUKAN') !== false || strlen($cleanAI) < 10) {
            return response()->json([
                'success' => false,
                'error'   => 'ISBN tidak ditemukan di semua sumber (Gramedia, Google Books, Open Library, dan AI). Silakan masukkan ISBN secara manual.',
            ]);
        }

        $sourceLabel = $provider === 'openrouter' ? 'AI (OpenRouter)' : 'AI (Gemini)';

        return response()->json([
            'success'    => true,
            'isbn'       => $cleanAI,
            'source'     => $sourceLabel,
            'confidence' => 'low',
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

            // 3. Fallback: Jika pencarian lewat ISBN gagal, coba cari lewat Judul + Pengarang
            if (!$thumbnail && request('judul') && request('pengarang')) {
                $judul = request('judul');
                $pengarang = request('pengarang');
                
                $query = urlencode("intitle:{$judul} inauthor:{$pengarang}");
                $resFallback = Http::get("https://www.googleapis.com/books/v1/volumes?q={$query}");
                
                if ($resFallback->ok() && isset($resFallback['items'])) {
                    foreach ($resFallback['items'] as $item) {
                        if (isset($item['volumeInfo']['imageLinks'])) {
                            $links = $item['volumeInfo']['imageLinks'];
                            $thumbnail = $links['large'] ??
                                $links['medium'] ??
                                $links['small'] ??
                                $links['thumbnail'] ??
                                $links['smallThumbnail'] ?? null;
                            if ($thumbnail) {
                                break;
                            }
                        }
                    }
                }
            }

            // 4. Jika tetap tidak ada cover
            if (!$thumbnail) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cover tidak tersedia untuk ISBN atau Judul buku ini.'
                ]);
            }

            // 4. Unduh & simpan gambar langsung ke public/cover_buku
            $thumbnail = str_replace('http://', 'https://', $thumbnail);
            
            $opts = [
                "http" => [
                    "method" => "GET",
                    "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3\r\n",
                    "timeout" => 7.0
                ]
            ];
            $context = stream_context_create($opts);
            $imageContent = @file_get_contents($thumbnail, false, $context);

            if (!$imageContent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengunduh gambar cover dari URL sumber. Kemungkinan link tidak valid atau diblokir.'
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

    public function fetchCoverByAI(Request $request)
    {
        $judul = $request->input('judul');
        $pengarang = $request->input('pengarang');
        $source = $request->input('source', 'gemini');

        if (!$judul || !$pengarang) {
            return response()->json([
                'success' => false,
                'message' => 'Judul dan pengarang harus diisi untuk mencari cover.'
            ]);
        }

        $url = null;

        // ================================================================
        // SOURCE: GRAMEDIA — Web Scraping langsung (tanpa AI)
        // ================================================================
        if ($source === 'gramedia') {
            try {
                // Generate slug dari judul buku: "Inyik Balang" → "inyik-balang"
                $slug = Str::slug($judul);

                $res = Http::timeout(10)->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'Accept-Language' => 'id-ID,id;q=0.9,en;q=0.8',
                ])->get("https://www.gramedia.com/products/{$slug}");

                if ($res->ok()) {
                    $html = $res->body();

                    // Strategi 1: Ambil dari meta tag og:image (paling reliable)
                    // Pattern: <meta property="og:image" ... content="https://cdn.gramedia.com/uploads/products/xxx.jpg" />
                    if (preg_match('/<meta\s+property=["\']og:image["\']\s+[^>]*content=["\']([^"\']+)["\']/i', $html, $match)) {
                        $url = $match[1];
                    }
                    // Variasi: content sebelum property
                    if (!$url && preg_match('/<meta\s+content=["\']([^"\']+)["\']\s+[^>]*property=["\']og:image["\']/i', $html, $match)) {
                        $url = $match[1];
                    }

                    // Strategi 2: Cari URL CDN Gramedia langsung dari HTML
                    // Pattern: https://cdn.gramedia.com/uploads/products/xxx.jpg
                    if (!$url && preg_match('/https?:\/\/cdn\.gramedia\.com\/uploads\/products\/[a-zA-Z0-9_\-]+\.(jpg|jpeg|png|webp)/i', $html, $match)) {
                        $url = $match[0];
                    }

                    // Strategi 3: Cari dari image.gramedia.net (CDN resize service)
                    // Pattern: https://image.gramedia.net/rs:fit:0:0/plain/https://cdn.gramedia.com/uploads/products/xxx.jpg
                    if (!$url && preg_match('/https?:\/\/image\.gramedia\.net\/[^"\']+\/plain\/(https?:\/\/cdn\.gramedia\.com\/uploads\/products\/[a-zA-Z0-9_\-]+\.(jpg|jpeg|png|webp))/i', $html, $match)) {
                        $url = $match[1]; // Ambil URL asli dari CDN, bukan yang di-resize
                    }
                }

                if (!$url) {
                    return response()->json([
                        'success' => false,
                        'message' => "Cover tidak ditemukan di Gramedia.com untuk buku \"{$judul}\". Pastikan judul buku sesuai dengan yang ada di gramedia.com/products/{$slug}"
                    ]);
                }

            } catch (\Exception $e) {
                Log::warning('Gramedia cover scraping gagal: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengakses Gramedia.com: ' . $e->getMessage()
                ]);
            }
        }

        // ================================================================
        // SOURCE: GEMINI — AI mencari URL cover dari web
        // ================================================================
        if ($source !== 'gramedia') {
            $prompt = "Berikan satu URL gambar cover buku yang valid, langsung, dan dapat diakses publik untuk buku berjudul \"$judul\" karya \"$pengarang\". 
Respons HANYA berupa URL gambar tersebut saja, tanpa penjelasan, tanpa format markdown, tanpa tanda kutip, dan tanpa teks tambahan lainnya. 
Pastikan URL tersebut langsung mengarah ke file gambar (format .jpg, .jpeg, .png, atau .webp) dari situs tepercaya seperti Goodreads atau Wikipedia.";

            $aiResult = $this->askAI($prompt);

            if (!$aiResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $aiResult['error']
                ]);
            }

            $url = trim($aiResult['text']);

            // Clean markdown formatting if AI returned it (e.g. `url` or [text](url))
            if (preg_match('/https?:\/\/[^\s\)\`\]]+/i', $url, $matches)) {
                $url = $matches[0];
            }

            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                return response()->json([
                    'success' => false,
                    'message' => 'AI menghasilkan URL yang tidak valid: ' . substr($url, 0, 100)
                ]);
            }
        }

        // ================================================================
        // DOWNLOAD & SIMPAN GAMBAR COVER
        // ================================================================
        try {
            $url = str_replace('http://', 'https://', $url);

            $opts = [
                "http" => [
                    "method" => "GET",
                    "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36\r\n",
                    "timeout" => 10.0
                ]
            ];
            $context = stream_context_create($opts);
            $imageContent = @file_get_contents($url, false, $context);

            if (!$imageContent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengunduh gambar cover dari URL: ' . substr($url, 0, 100)
                ]);
            }

            // Generate filename based on title
            $safeTitle = Str::slug($judul);
            $sourceLabel = $source === 'gramedia' ? 'gramedia' : 'ai';
            $filename = "cover_{$sourceLabel}_{$safeTitle}_" . Str::random(5) . '.jpg';
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
                'message' => 'Gagal memproses gambar: ' . $e->getMessage()
            ]);
        }
    }
}
