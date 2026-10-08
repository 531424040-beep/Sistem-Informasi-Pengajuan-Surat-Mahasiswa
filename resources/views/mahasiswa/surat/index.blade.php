@extends('layouts.app')

@section('title', 'Ajukan Surat')

@section('content')
    <form method="GET" action="{{ route('mahasiswa.surat.index') }}" class="mb-4 flex max-w-md gap-2">
        <input type="text" name="q" value="{{ $keyword }}" placeholder="Cari jenis surat..."
            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-sky-500">
        <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
            Cari
        </button>
    </form>

    <div class="grid gap-4 md:grid-cols-2">
        @forelse ($jenisSurats as $jenisSurat)
            <div class="flex flex-col rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-base font-semibold text-slate-800">{{ $jenisSurat->nama_surat }}</h2>
                <p class="mt-2 flex-1 text-sm text-slate-600">{{ \Illuminate\Support\Str::limit($jenisSurat->deskripsi, 140) }}</p>
                <div class="mt-4 flex items-center justify-between">
                    <span class="text-xs text-slate-500">{{ $jenisSurat->persyaratans_count }} persyaratan</span>
                    <a href="{{ route('mahasiswa.surat.show', $jenisSurat) }}"
                        class="rounded-lg bg-sky-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-sky-700">
                        Lihat & Ajukan
                    </a>
                </div>
            </div>
        @empty
            <p class="col-span-full rounded-xl border border-slate-200 bg-white p-8 text-center text-sm text-slate-500">
                Tidak ada jenis surat yang tersedia.
            </p>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $jenisSurats->links() }}
    </div>
@endsection
