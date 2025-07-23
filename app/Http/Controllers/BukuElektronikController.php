<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BukuElektronik;

class BukuElektronikController extends Controller
{
    public function index(Request $request)
    {
        $activeMenu = 'katalog';

        $search = $request->input('search');
        $category = $request->input('category', 'judul'); // default kolom 'judul'

        $query = BukuElektronik::orderBy('created_at', 'desc');

        if ($search && $category !== 'all') {
            $query->where(function ($q) use ($search, $category) {
                switch ($category) {
                    case 'judul':
                        $q->where('judul', 'like', "%{$search}%");
                        break;
                    case 'penulis':
                        $q->where('penulis', 'like', "%{$search}%");
                        break;
                    case 'kategori':
                        $q->where('kategori', 'like', "%{$search}%");
                        break;
                    case 'kelas':
                        $q->where('kelas', 'like', "%{$search}%");
                        break;
                    case 'kurikulum':
                        $q->where('kurikulum', 'like', "%{$search}%");
                        break;
                    default:
                        // Jika category tidak dikenali, fallback ke judul
                        $q->where('judul', 'like', "%{$search}%");
                        break;
                }
            });
        }

        $buku = $query->paginate(10)->appends([
            'search' => $search,
            'category' => $category
        ]);

        return view('admin.buku-elektronik.index', compact('activeMenu', 'buku', 'search', 'category'));
    }

    public function create()
    {
        $activeMenu = 'katalog';
        return view('admin.buku-elektronik.create', compact('activeMenu'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'penulis' => 'nullable|string|max:255',
            'kelas' => 'required|string|max:50',
            'kategori' => 'required|string|max:100',
            'kurikulum' => 'nullable|string|max:100',
            'pdf_path' => 'nullable|file|mimes:pdf|max:10240', // max 10MB
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // max 5MB
        ]);

        // Handle upload PDF
        if ($request->hasFile('pdf_path')) {
            $pdfFile = $request->file('pdf_path');
            $pdfFileName = uniqid() . '-' . $pdfFile->getClientOriginalName();
            $pdfFile->move(public_path('buku-pdf'), $pdfFileName);
            $validated['pdf_path'] = 'buku-pdf/' . $pdfFileName;
        }

        // Handle upload Cover Image
        if ($request->hasFile('cover_image')) {
            $coverFile = $request->file('cover_image');
            $coverFileName = uniqid() . '-' . $coverFile->getClientOriginalName();
            $coverFile->move(public_path('buku-cover'), $coverFileName);
            $validated['cover_image'] = 'buku-cover/' . $coverFileName;
        }

        // Simpan ke database
        BukuElektronik::create($validated);

        // Redirect ke index
        return redirect()->route('admin.buku-elektronik.index')
            ->with('success', 'Buku elektronik berhasil ditambahkan.');
    }



    public function edit($id)
    {
        $activeMenu = 'katalog';
        $buku = BukuElektronik::findOrFail($id);
        return view('admin.buku-elektronik.edit', compact('activeMenu', 'buku'));
    }


    public function update(Request $request, $id)
    {
        $buku = BukuElektronik::findOrFail($id);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'penulis' => 'nullable|string|max:255',
            'kelas' => 'required|string|max:50',
            'kategori' => 'required|string|max:100',
            'kurikulum' => 'nullable|string|max:100',
            'pdf_path' => 'nullable|file|mimes:pdf|max:10240',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        // Handle upload PDF
        if ($request->hasFile('pdf_path')) {
            // Hapus file lama jika ada
            if ($buku->pdf_path && file_exists(public_path($buku->pdf_path))) {
                unlink(public_path($buku->pdf_path));
            }

            $pdfFile = $request->file('pdf_path');
            $pdfFileName = uniqid() . '-' . $pdfFile->getClientOriginalName();
            $pdfFile->move(public_path('buku-pdf'), $pdfFileName);
            $validated['pdf_path'] = 'buku-pdf/' . $pdfFileName;
        }

        // Handle upload Cover Image
        if ($request->hasFile('cover_image')) {
            // Hapus file lama jika ada
            if ($buku->cover_image && file_exists(public_path($buku->cover_image))) {
                unlink(public_path($buku->cover_image));
            }

            $coverFile = $request->file('cover_image');
            $coverFileName = uniqid() . '-' . $coverFile->getClientOriginalName();
            $coverFile->move(public_path('buku-cover'), $coverFileName);
            $validated['cover_image'] = 'buku-cover/' . $coverFileName;
        }

        // Update data
        $buku->update($validated);

        return redirect()->route('admin.buku-elektronik.index')
            ->with('success', 'Buku elektronik berhasil diperbarui.');
    }



    public function destroy($id)
    {
        $buku = BukuElektronik::findOrFail($id);
        $buku->delete();

        return redirect()->route('admin.buku-elektronik.index')
            ->with('success', 'Buku elektronik berhasil dihapus.');
    }
}
