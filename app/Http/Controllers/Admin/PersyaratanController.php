<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PersyaratanRequest;
use App\Models\JenisSurat;
use App\Models\Persyaratan;
use Illuminate\Http\RedirectResponse;

class PersyaratanController extends Controller
{
    public function store(PersyaratanRequest $request, JenisSurat $jenisSurat): RedirectResponse
    {
        $jenisSurat->persyaratans()->create($request->validated());

        return back()->with('success', 'Persyaratan berhasil ditambahkan.');
    }

    public function update(PersyaratanRequest $request, JenisSurat $jenisSurat, Persyaratan $persyaratan): RedirectResponse
    {
        abort_unless($persyaratan->jenis_surat_id === $jenisSurat->id, 404);

        $persyaratan->update($request->validated());

        return back()->with('success', 'Persyaratan berhasil diperbarui.');
    }

    public function destroy(JenisSurat $jenisSurat, Persyaratan $persyaratan): RedirectResponse
    {
        abort_unless($persyaratan->jenis_surat_id === $jenisSurat->id, 404);

        $persyaratan->delete();

        return back()->with('success', 'Persyaratan berhasil dihapus.');
    }
}
