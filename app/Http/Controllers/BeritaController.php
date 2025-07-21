<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Berita;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BeritaController extends Controller
{
    public function index(Request $request)
    {
        $activeMenu = 'berita';

        $search = $request->input('search');
        $category = $request->input('category', '');

        $query = Berita::query();

        if ($search && $category && in_array($category, ['judul', 'penulis', 'status'])) {
            $query->where($category, 'like', '%' . $search . '%');
        }

        $berita = $query->latest()->paginate(10)->withQueryString();

        return view('admin.berita.index', compact('berita', 'activeMenu', 'search', 'category'));
    }

    public function create()
    {
        $activeMenu = 'berita';
        return view('admin.berita.create', compact('activeMenu'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status' => 'required|in:draft,publish',
            'penulis' => 'nullable|string|max:100',
        ]);

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        Berita::create($validated);

        return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil ditambahkan.');
    }


    public function edit($id)
    {
        $activeMenu = 'berita';
        $berita = Berita::findOrFail($id);
        return view('admin.berita.edit', compact('berita', 'activeMenu'));
    }

    public function update(Request $request, $id)
    {
        $berita = Berita::findOrFail($id);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status' => 'required|in:draft,publish',
            'penulis' => 'nullable|string|max:100',
            'thumbnail_url' => 'nullable|string|max:255', // input hidden untuk simpan file lama
        ]);

        if ($request->hasFile('thumbnail')) {
            // Hapus thumbnail lama
            if ($berita->thumbnail && Storage::exists('public/' . $berita->thumbnail)) {
                Storage::delete('public/' . $berita->thumbnail);
            }

            $validated['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        } elseif ($request->filled('thumbnail_url')) {
            // Jika tidak upload baru, simpan yang lama
            $validated['thumbnail'] = $request->thumbnail_url;
        }

        $berita->update($validated);

        return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil diperbarui.');
    }


    public function destroy($id)
    {
        $berita = Berita::findOrFail($id);

        // Hapus thumbnail dari storage
        if ($berita->thumbnail && Storage::exists('public/' . $berita->thumbnail)) {
            Storage::delete('public/' . $berita->thumbnail);
        }

        $berita->delete();

        return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil dihapus.');
    }


    public function indexPublish()
    {
        $berita = Berita::where('status', 'publish')->latest()->get();

        // Ambil berita pertama sebagai Hero (optional)
        $hero = $berita->first();

        // Popular posts = semua berita publish, random urutannya
        $popular = Berita::where('status', 'publish')->inRandomOrder()->get();

        return view('berita.index', compact('berita', 'hero', 'popular'));
    }

    public function show($id)
    {
        $berita = Berita::where('status', 'publish')->findOrFail($id);

        // Ambil 4 berita populer lainnya untuk ditampilkan di bawah
        $related = Berita::where('status', 'publish')
            ->where('id', '!=', $berita->id)
            ->inRandomOrder()
            ->take(4)
            ->get();

        return view('berita.show', compact('berita', 'related'));
    }
}
