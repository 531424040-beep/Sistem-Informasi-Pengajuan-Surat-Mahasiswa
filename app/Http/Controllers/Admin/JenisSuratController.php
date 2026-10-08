<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\JenisSuratRequest;
use App\Models\JenisSurat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JenisSuratController extends Controller
{
    /**
     * Daftar semua jenis surat.
     */
    public function index(Request $request): View
    {
        $jenisSurats = JenisSurat::query()
            ->withCount('persyaratans')
            ->when($request->filled('q'), fn ($query) => $query->where('nama_surat', 'like', '%'.$request->query('q').'%'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.jenis-surat.index', compact('jenisSurats'));
    }

    public function create(): View
    {
        return view('admin.jenis-surat.create');
    }

    public function store(JenisSuratRequest $request): RedirectResponse
    {
        $jenisSurat = JenisSurat::create($request->validated());

        return redirect()
            ->route('admin.jenis-surat.edit', $jenisSurat)
            ->with('success', 'Jenis surat berhasil ditambahkan. Silakan tambahkan persyaratannya.');
    }

    public function edit(JenisSurat $jenisSurat): View
    {
        $jenisSurat->load('persyaratans');

        return view('admin.jenis-surat.edit', compact('jenisSurat'));
    }

    public function update(JenisSuratRequest $request, JenisSurat $jenisSurat): RedirectResponse
    {
        $jenisSurat->update($request->validated());

        return back()->with('success', 'Jenis surat berhasil diperbarui.');
    }

    /**
     * Hapus jenis surat (dilindungi FK bila masih ada pengajuan).
     */
    public function destroy(JenisSurat $jenisSurat): RedirectResponse
    {
        if ($jenisSurat->pengajuans()->exists()) {
            return back()->with('error', 'Jenis surat tidak dapat dihapus karena sudah digunakan dalam pengajuan. Nonaktifkan saja.');
        }

        $jenisSurat->delete();

        return redirect()->route('admin.jenis-surat.index')->with('success', 'Jenis surat berhasil dihapus.');
    }
}
