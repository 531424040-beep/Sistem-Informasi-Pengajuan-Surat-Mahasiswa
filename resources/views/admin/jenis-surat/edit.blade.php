@extends('layouts.app')

@section('title', 'Edit Jenis Surat')

@section('content')
    <a href="{{ route('admin.jenis-surat.index') }}" class="mb-4 inline-block text-sm text-sky-600 hover:underline">&larr;
        Kembali ke daftar jenis surat</a>

    <div class="grid gap-4 lg:grid-cols-2">
        {{-- Data Jenis Surat --}}
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-800">Data Jenis Surat</h2>

            @if ($errors->any())
                <div class="mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.jenis-surat.update', $jenisSurat) }}" class="mt-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="nama_surat" class="mb-1 block text-sm font-medium text-slate-700">Nama Surat <span
                            class="text-red-500">*</span></label>
                    <input type="text" id="nama_surat" name="nama_surat"
                        value="{{ old('nama_surat', $jenisSurat->nama_surat) }}" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-sky-500">
                </div>
                <div>
                    <label for="deskripsi" class="mb-1 block text-sm font-medium text-slate-700">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" rows="3"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-sky-500">{{ old('deskripsi', $jenisSurat->deskripsi) }}</textarea>
                </div>
                <div>
                    <label for="status" class="mb-1 block text-sm font-medium text-slate-700">Status <span
                            class="text-red-500">*</span></label>
                    <select id="status" name="status" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-sky-500">
                        <option value="1" {{ old('status', $jenisSurat->status) == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('status', $jenisSurat->status) == '0' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>

                <button type="submit"
                    class="rounded-lg bg-sky-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-sky-700">
                    Simpan Perubahan
                </button>
            </form>

            <form method="POST" action="{{ route('admin.jenis-surat.destroy', $jenisSurat) }}" class="mt-6 border-t border-slate-100 pt-4"
                onsubmit="return confirm('Yakin ingin menghapus jenis surat ini? Tindakan tidak dapat dibatalkan.')">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="rounded-lg border border-red-300 px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-50">
                    Hapus Jenis Surat
                </button>
                <p class="mt-2 text-xs text-slate-500">Jenis surat yang sudah digunakan dalam pengajuan tidak dapat
                    dihapus.</p>
            </form>
        </div>

        {{-- Persyaratan --}}
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-800">Daftar Persyaratan</h2>

            <ul class="mt-4 divide-y divide-slate-100">
                @forelse ($jenisSurat->persyaratans as $persyaratan)
                    <li class="py-3">
                        <form method="POST"
                            action="{{ route('admin.persyaratan.update', [$jenisSurat, $persyaratan]) }}"
                            class="flex flex-wrap items-center gap-2">
                            @csrf
                            @method('PUT')
                            <input type="text" name="nama_persyaratan" value="{{ $persyaratan->nama_persyaratan }}"
                                required
                                class="flex-1 rounded-lg border border-slate-300 px-3 py-1.5 text-sm focus:border-sky-500 focus:ring-sky-500">
                            <select name="wajib"
                                class="rounded-lg border border-slate-300 px-2 py-1.5 text-sm focus:border-sky-500 focus:ring-sky-500">
                                <option value="1" {{ $persyaratan->wajib ? 'selected' : '' }}>Wajib</option>
                                <option value="0" {{ $persyaratan->wajib ? '' : 'selected' }}>Opsional</option>
                            </select>
                            <button type="submit"
                                class="rounded-lg bg-slate-800 px-3 py-1.5 text-sm font-medium text-white hover:bg-slate-700">Simpan</button>
                        </form>
                        <form method="POST"
                            action="{{ route('admin.persyaratan.destroy', [$jenisSurat, $persyaratan]) }}" class="mt-2"
                            onsubmit="return confirm('Hapus persyaratan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs font-medium text-red-600 hover:underline">Hapus
                                persyaratan</button>
                        </form>
                    </li>
                @empty
                    <li class="py-3 text-sm text-slate-500">Belum ada persyaratan. Tambahkan pada formulir di bawah.</li>
                @endforelse
            </ul>

            <form method="POST" action="{{ route('admin.persyaratan.store', $jenisSurat) }}"
                class="mt-6 space-y-3 border-t border-slate-100 pt-4">
                @csrf
                <p class="text-sm font-medium text-slate-700">Tambah Persyaratan</p>
                <input type="text" name="nama_persyaratan" placeholder="Nama persyaratan" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-sky-500">
                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="hidden" name="wajib" value="0">
                    <input type="checkbox" name="wajib" value="1" class="rounded border-slate-300" checked>
                    Dokumen wajib diunggah
                </label>
                <button type="submit"
                    class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                    Tambah Persyaratan
                </button>
            </form>
        </div>
    </div>
@endsection
