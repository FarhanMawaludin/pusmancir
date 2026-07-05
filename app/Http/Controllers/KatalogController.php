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



    private function askAI($prompt, $temperature = null, $provider = null, $useSearch = false)
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
        // FORCED PROVIDER: GEMINI
        // ====================================================================
        if ($provider === 'gemini') {
            if (!$geminiApiKey) {
                return [
                    'success' => false,
                    'error' => 'API Key Gemini belum dikonfigurasi di file .env.'
                ];
            }
            return $this->executeGemini($prompt, $temperature, $geminiApiKey, $useSearch);
        }

        // ====================================================================
        // DEFAULT FLOW (No Provider Specified)
        // ====================================================================
        if ($geminiApiKey) {
            $geminiRes = $this->executeGemini($prompt, $temperature, $geminiApiKey, $useSearch);
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

    private function executeGemini($prompt, $temperature, $apiKey, $useSearch = false)
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

            if ($useSearch) {
                $payload['tools'] = [
                    ['google_search' => new \stdClass()]
                ];
            }

            if ($temperature !== null) {
                $payload['generationConfig'] = [
                    'temperature' => $temperature
                ];
            }

            $response = Http::timeout(60)->connectTimeout(15)->withHeaders([
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
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            logger()->error('Gemini API Connection Timeout:', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => 'Koneksi ke Gemini API timeout. Koneksi internet terganggu atau server Google sedang sibuk.'
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
            $model = env('OPENROUTER_MODEL', 'openrouter/free');
            
            logger()->info('OpenRouter: Memulai request', ['model' => $model, 'prompt_length' => strlen($prompt)]);

            $response = Http::timeout(120)->connectTimeout(15)->withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $apiKey,
                'HTTP-Referer' => config('app.url', 'http://localhost'),
                'X-Title' => config('app.name', 'PUSMANCIR'),
            ])->post('https://openrouter.ai/api/v1/chat/completions', [
                'model' => $model,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ],
                'temperature' => $temperature !== null ? $temperature : 1.0
            ]);

            logger()->info('OpenRouter: Response diterima', ['status' => $response->status()]);

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
                logger()->warning('OpenRouter: Respon kosong', ['response_data' => json_encode($data)]);
                return [
                    'success' => false,
                    'error' => 'Respon tidak tersedia dari OpenRouter.'
                ];
            }

            logger()->info('OpenRouter: Berhasil mendapat respon', ['text_length' => strlen($text)]);

            return [
                'success' => true,
                'text' => trim($text)
            ];
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            logger()->error('OpenRouter Connection Timeout:', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => 'Koneksi ke OpenRouter timeout. Server AI sedang sibuk, silakan coba lagi.'
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

        $aiResult = $this->askAI($prompt, null, $provider, true);

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

        $aiResult = $this->askAI($prompt, 0.3, $provider, true);

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

        $penerbitInfo = $penerbit ? " diterbitkan oleh \"{$penerbit}\"" : '';
        $prompt = "Lakukan penelusuran Google Search untuk mencari buku berjudul \"{$judul}\" karya \"{$pengarang}\"{$penerbitInfo}.
Temukan nomor ISBN (13 digit) yang terdaftar resmi untuk buku ini. 
PENTING: Pastikan Anda membaca digit terakhir (check digit) dengan sangat teliti dari hasil pencarian web. JANGAN menebak atau mengubah angka belakangnya. 
Respons HANYA berupa 13 digit nomor ISBN tersebut saja (contoh: 9786231342355), tanpa penjelasan, tanpa format markdown, tanpa tanda kutip, dan tanpa teks tambahan lainnya. 
Jika tidak yakin atau tidak ditemukan, jawab dengan: TIDAK_DITEMUKAN";

        $aiResult = $this->askAI($prompt, 0.0, $provider, true);

        if (!$aiResult['success']) {
            return response()->json([
                'success' => false,
                'error'   => $aiResult['error'],
            ]);
        }

        $aiIsbn   = trim($aiResult['text']);
        $cleanAI  = preg_replace('/[^0-9]/', '', $aiIsbn);

        if (stripos($aiIsbn, 'TIDAK_DITEMUKAN') !== false || strlen($cleanAI) < 10) {
            return response()->json([
                'success' => false,
                'error'   => 'ISBN tidak ditemukan. Silakan coba lagi atau masukkan ISBN secara manual.',
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
    public function fetchCoverByAI(Request $request)
    {
        $judul = $request->input('judul');
        $pengarang = $request->input('pengarang');
        $source = $request->input('source', 'gemini');
        $isbn = $request->input('isbn', '');

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
            $prompt = "Cari buku berjudul \"$judul\" karya \"$pengarang\" di Google Books atau internet. 
Temukan nomor ISBN (10 atau 13 digit) untuk buku tersebut. 
Respons HANYA berupa URL gambar cover dari Google Books dengan format berikut:
https://books.google.com/books/content?vid=ISBN[NOMOR_ISBN_TANPA_SPASI_DAN_STRIP]&printsec=frontcover&img=1&zoom=1
JANGAN berikan teks lain, penjelasan, markdown, atau kutip. Hanya URL tersebut saja. Contoh format: https://books.google.com/books/content?vid=ISBN9786231342355&printsec=frontcover&img=1&zoom=1";

            // Teruskan source sebagai provider agar OpenRouter/Gemini sesuai pilihan user
            $provider = ($source === 'openrouter') ? 'openrouter' : 'gemini';
            $aiResult = $this->askAI($prompt, 0.0, $provider, true);

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
            $imageContent = null;

            if ($url) {
                $url = str_replace('http://', 'https://', $url);
                try {
                    $imgResponse = Http::timeout(15)->withoutVerifying()->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
                    ])->get($url);
                    if ($imgResponse->successful()) {
                        $imageContent = $imgResponse->body();
                    }
                } catch (\Exception $e) {
                    logger()->error('Gagal mengunduh cover via HTTP: ' . $e->getMessage());
                }
            }

            // Fallback 1: Coba dengan Gramedia Web Scraper jika Google Books mengembalikan gambar kosong / gagal
            if (!$imageContent || strlen($imageContent) < 2000) {
                try {
                    $slug = Str::slug($judul);
                    $gramediaRes = Http::timeout(10)->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                        'Accept-Language' => 'id-ID,id;q=0.9,en;q=0.8',
                    ])->get("https://www.gramedia.com/products/{$slug}");

                    if ($gramediaRes->ok()) {
                        $html = $gramediaRes->body();
                        $gramediaUrl = null;

                        if (preg_match('/<meta\s+property=["\']og:image["\']\s+[^>]*content=["\']([^"\']+)["\']/i', $html, $match)) {
                            $gramediaUrl = $match[1];
                        }
                        if (!$gramediaUrl && preg_match('/<meta\s+content=["\']([^"\']+)["\']\s+[^>]*property=["\']og:image["\']/i', $html, $match)) {
                            $gramediaUrl = $match[1];
                        }
                        if (!$gramediaUrl && preg_match('/https?:\/\/cdn\.gramedia\.com\/uploads\/products\/[a-zA-Z0-9_\-]+\.(jpg|jpeg|png|webp)/i', $html, $match)) {
                            $gramediaUrl = $match[0];
                        }
                        if (!$gramediaUrl && preg_match('/https?:\/\/image\.gramedia\.net\/[^"\']+\/plain\/(https?:\/\/cdn\.gramedia\.com\/uploads\/products\/[a-zA-Z0-9_\-]+\.(jpg|jpeg|png|webp))/i', $html, $match)) {
                            $gramediaUrl = $match[1];
                        }

                        if ($gramediaUrl) {
                            $gramediaUrl = str_replace('http://', 'https://', $gramediaUrl);
                            $imgResponse = Http::timeout(15)->withoutVerifying()->get($gramediaUrl);
                            if ($imgResponse->successful() && strlen($imgResponse->body()) > 2000) {
                                $imageContent = $imgResponse->body();
                                $url = $gramediaUrl; // Update URL untuk penamaan file
                            }
                        }
                    }
                } catch (\Exception $ex) {
                    Log::warning('Gramedia fallback scraping gagal: ' . $ex->getMessage());
                }
            }

            // Fallback 2: Coba cari dari Open Library (jika ISBN tersedia)
            if ((!$imageContent || strlen($imageContent) < 2000) && !empty($isbn)) {
                try {
                    $openLibUrl = "https://covers.openlibrary.org/b/isbn/{$isbn}-L.jpg";
                    $checkImage = @getimagesize($openLibUrl);
                    if ($checkImage !== false) {
                        $imgResponse = Http::timeout(15)->withoutVerifying()->get($openLibUrl);
                        if ($imgResponse->successful() && strlen($imgResponse->body()) > 2000) {
                            $imageContent = $imgResponse->body();
                            $url = $openLibUrl;
                        }
                    }
                } catch (\Exception $ex) {
                    Log::warning('Open Library fallback gagal: ' . $ex->getMessage());
                }
            }

            if (!$imageContent || strlen($imageContent) < 2000) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengunduh cover dari semua sumber (Google Books, Gramedia Scraper, dan Open Library).'
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
