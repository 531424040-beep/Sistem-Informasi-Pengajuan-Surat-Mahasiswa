@extends('layouts.app')

@section('title', 'Kelola Jenis Surat')

@section('content')
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form method="GET" action="{{ route('admin.jenis-surat.index') }}" class="flex gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari jenis surat..."
                class="w-64 rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-sky-500">
            <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                Cari
            </button>
        </form>
        <a href="{{ route('admin.jenis-surat.create') }}"
            class="rounded-lg bg-sky-600 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-sky-700">
            + Tambah Jenis Surat
        </a>
    </div>

    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-5 py-3">Nama Surat</th>
                    <th class="px-5 py-3">Deskripsi</th>
                    <th class="px-5 py-3">Persyaratan</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($jenisSurats as $jenisSurat)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-3 font-medium text-slate-800">{{ $jenisSurat->nama_surat }}</td>
                        <td class="max-w-xs px-5 py-3 text-slate-600">{{ \Illuminate\Support\Str::limit($jenisSurat->deskripsi, 80) }}</td>
                        <td class="px-5 py-3">{{ $jenisSurat->persyaratans_count }} item</td>
                        <td class="px-5 py-3">
                            @if ($jenisSurat->isActive())
                                <span
                                    class="inline-flex rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-700">Aktif</span>
                            @else
                                <span
                                    class="inline-flex rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('admin.jenis-surat.edit', $jenisSurat) }}"
                                class="font-medium text-sky-600 hover:underline">Kelola</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center text-slate-500">Belum ada jenis surat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $jenisSurats->links() }}
    </div>
@endsection
