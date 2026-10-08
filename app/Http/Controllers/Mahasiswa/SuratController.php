<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\JenisSurat;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SuratController extends Controller
{
    /**
     * Daftar jenis surat yang aktif.
     */
    public function index(Request $request): View
    {
        $keyword = $request->query('q');

        $jenisSurats = JenisSurat::query()
            ->where('status', true)
            ->when($keyword, fn ($query) => $query->where('nama_surat', 'like', "%{$keyword}%"))
            ->withCount('persyaratans')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('mahasiswa.surat.index', compact('jenisSurats', 'keyword'));
    }

    /**
     * Detail jenis surat beserta persyaratannya.
     */
    public function show(JenisSurat $jenisSurat): View
    {
        abort_unless($jenisSurat->isActive(), 404);

        $jenisSurat->load('persyaratans');

        return view('mahasiswa.surat.show', compact('jenisSurat'));
    }
}
