@extends('layouts.app')

@section('title', 'Tambah Jenis Surat')

@section('content')
    <a href="{{ route('admin.jenis-surat.index') }}" class="mb-4 inline-block text-sm text-sky-600 hover:underline">&larr;
        Kembali ke daftar jenis surat</a>

    <div class="max-w-xl rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-800">Tambah Jenis Surat</h2>

        @if ($errors->any())
            <div class="mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.jenis-surat.store') }}" class="mt-6 space-y-4">
            @csrf
            <div>
                <label for="nama_surat" class="mb-1 block text-sm font-medium text-slate-700">Nama Surat <span
                        class="text-red-500">*</span></label>
                <input type="text" id="nama_surat" name="nama_surat" value="{{ old('nama_surat') }}" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-sky-500">
            </div>
            <div>
                <label for="deskripsi" class="mb-1 block text-sm font-medium text-slate-700">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" rows="3"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-sky-500">{{ old('deskripsi') }}</textarea>
            </div>
            <div>
                <label for="status" class="mb-1 block text-sm font-medium text-slate-700">Status <span
                        class="text-red-500">*</span></label>
                <select id="status" name="status" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-sky-500">
                    <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="rounded-lg bg-sky-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-sky-700">
                    Simpan
                </button>
                <a href="{{ route('admin.jenis-surat.index') }}"
                    class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Batal</a>
            </div>
        </form>
    </div>
@endsection
