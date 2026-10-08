@extends('layouts.app')

@section('title', 'Laporan Pengajuan')

@section('content')
    <form method="GET" action="{{ route('admin.laporan') }}" class="mb-4 flex flex-wrap items-end gap-2">
        <div>
            <label class="mb-1 block text-xs text-slate-500">Periode Dari</label>
            <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}"
                class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-sky-500">
        </div>
        <div>
            <label class="mb-1 block text-xs text-slate-500">Periode Sampai</label>
            <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}"
                class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-sky-500">
        </div>
        <div>
            <label class="mb-1 block text-xs text-slate-500">Jenis Surat</label>
            <select name="jenis_surat_id" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-sky-500">
                <option value="">Semua</option>
                @foreach ($jenisSurats as $jenisSurat)
                    <option value="{{ $jenisSurat->id }}" {{ request('jenis_surat_id') == $jenisSurat->id ? 'selected' : '' }}>
                        {{ $jenisSurat->nama_surat }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs text-slate-500">Status</label>
            <select name="status" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-sky-500">
                <option value="">Semua</option>
                @foreach ($statuses as $value)
                    <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $value }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
            Tampilkan Laporan
        </button>
    </form>

    <div class="mb-6 grid grid-cols-2 gap-4 md:grid-cols-5">
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs text-slate-500">Total Pengajuan</p>
            <p class="mt-1 text-2xl font-bold text-slate-800">{{ $stats['total'] }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs text-slate-500">Selesai</p>
            <p class="mt-1 text-2xl font-bold text-emerald-600">{{ $stats['selesai'] }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs text-slate-500">Diproses</p>
            <p class="mt-1 text-2xl font-bold text-indigo-600">{{ $stats['diproses'] }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs text-slate-500">Perlu Perbaikan</p>
            <p class="mt-1 text-2xl font-bold text-orange-600">{{ $stats['perlu_perbaikan'] }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs text-slate-500">Ditolak</p>
            <p class="mt-1 text-2xl font-bold text-red-600">{{ $stats['ditolak'] }}</p>
        </div>
    </div>

    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-5 py-3">Nomor</th>
                    <th class="px-5 py-3">Mahasiswa</th>
                    <th class="px-5 py-3">Jenis Surat</th>
                    <th class="px-5 py-3">Tanggal</th>
                    <th class="px-5 py-3">Keperluan</th>
                    <th class="px-5 py-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($pengajuans as $pengajuan)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-3 font-medium text-slate-800">{{ $pengajuan->nomor_pengajuan }}</td>
                        <td class="px-5 py-3">
                            {{ $pengajuan->user->name }}
                            <p class="text-xs text-slate-500">{{ $pengajuan->user->nim ?: '-' }}</p>
                        </td>
                        <td class="px-5 py-3">{{ $pengajuan->jenisSurat->nama_surat }}</td>
                        <td class="px-5 py-3">{{ $pengajuan->tanggal_pengajuan->format('d/m/Y') }}</td>
                        <td class="px-5 py-3">{{ \Illuminate\Support\Str::limit($pengajuan->keperluan, 50) }}</td>
                        <td class="px-5 py-3"><x-status-badge :status="$pengajuan->status->value" /></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-slate-500">Tidak ada data laporan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $pengajuans->links() }}
    </div>
@endsection
