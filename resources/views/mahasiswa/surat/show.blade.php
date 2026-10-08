@extends('layouts.app')

@section('title', 'Detail Jenis Surat')

@section('content')
    <a href="{{ route('mahasiswa.surat.index') }}" class="mb-4 inline-block text-sm text-sky-600 hover:underline">&larr;
        Kembali ke daftar surat</a>

    <div class="max-w-2xl rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-800">{{ $jenisSurat->nama_surat }}</h2>
        <p class="mt-2 text-sm text-slate-600">{{ $jenisSurat->deskripsi ?: 'Tidak ada deskripsi.' }}</p>

        <h3 class="mt-6 text-sm font-semibold text-slate-800">Persyaratan</h3>
        <ul class="mt-2 space-y-2">
            @forelse ($jenisSurat->persyaratans as $persyaratan)
                <li class="flex items-center gap-2 text-sm text-slate-700">
                    <svg class="h-4 w-4 text-sky-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ $persyaratan->nama_persyaratan }}
                    <span
                        class="rounded-full {{ $persyaratan->wajib ? 'bg-red-100 text-red-700' : 'bg-slate-100 text-slate-600' }} px-2 py-0.5 text-xs">
                        {{ $persyaratan->wajib ? 'Wajib' : 'Opsional' }}
                    </span>
                </li>
            @empty
                <li class="text-sm text-slate-500">Tidak ada persyaratan khusus.</li>
            @endforelse
        </ul>

        <div class="mt-6">
            <a href="{{ route('mahasiswa.pengajuan.create', $jenisSurat) }}"
                class="inline-block rounded-lg bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-700">
                Ajukan Surat Ini
            </a>
        </div>
    </div>
@endsection
