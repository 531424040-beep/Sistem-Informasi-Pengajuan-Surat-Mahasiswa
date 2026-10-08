@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
    {{-- Statistik --}}
    <div class="mb-6 grid grid-cols-2 gap-4 md:grid-cols-4 lg:grid-cols-7">
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs text-slate-500">Total Pengajuan</p>
            <p class="mt-1 text-2xl font-bold text-slate-800">{{ $stats['total'] }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs text-slate-500">Baru</p>
            <p class="mt-1 text-2xl font-bold text-sky-600">{{ $stats['baru'] }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs text-slate-500">Diperiksa</p>
            <p class="mt-1 text-2xl font-bold text-amber-600">{{ $stats['diperiksa'] }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs text-slate-500">Perlu Perbaikan</p>
            <p class="mt-1 text-2xl font-bold text-orange-600">{{ $stats['perlu_perbaikan'] }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs text-slate-500">Diproses</p>
            <p class="mt-1 text-2xl font-bold text-indigo-600">{{ $stats['diproses'] }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs text-slate-500">Selesai</p>
            <p class="mt-1 text-2xl font-bold text-emerald-600">{{ $stats['selesai'] }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs text-slate-500">Ditolak</p>
            <p class="mt-1 text-2xl font-bold text-red-600">{{ $stats['ditolak'] }}</p>
        </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-2">
        {{-- Perlu tindakan --}}
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="text-sm font-semibold text-slate-800">Membutuhkan Tindakan Admin</h2>
            </div>
            <ul class="divide-y divide-slate-100">
                @forelse ($perluTindakan as $pengajuan)
                    <li class="flex items-center justify-between px-5 py-3">
                        <div>
                            <p class="text-sm font-medium text-slate-800">{{ $pengajuan->nomor_pengajuan }} ·
                                {{ $pengajuan->jenisSurat->nama_surat }}</p>
                            <p class="text-xs text-slate-500">{{ $pengajuan->user->name }}
                                ({{ $pengajuan->user->nim ?: '-' }})</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <x-status-badge :status="$pengajuan->status->value" />
                            <a href="{{ route('admin.pengajuan.show', $pengajuan) }}"
                                class="text-sm font-medium text-sky-600 hover:underline">Proses</a>
                        </div>
                    </li>
                @empty
                    <li class="px-5 py-8 text-center text-sm text-slate-500">Tidak ada pengajuan yang menunggu.</li>
                @endforelse
            </ul>
        </div>

        {{-- Terbaru --}}
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="text-sm font-semibold text-slate-800">Pengajuan Terbaru</h2>
            </div>
            <ul class="divide-y divide-slate-100">
                @forelse ($pengajuanTerbaru as $pengajuan)
                    <li class="flex items-center justify-between px-5 py-3">
                        <div>
                            <p class="text-sm font-medium text-slate-800">{{ $pengajuan->nomor_pengajuan }} ·
                                {{ $pengajuan->jenisSurat->nama_surat }}</p>
                            <p class="text-xs text-slate-500">{{ $pengajuan->tanggal_pengajuan->format('d/m/Y') }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <x-status-badge :status="$pengajuan->status->value" />
                            <a href="{{ route('admin.pengajuan.show', $pengajuan) }}"
                                class="text-sm font-medium text-sky-600 hover:underline">Detail</a>
                        </div>
                    </li>
                @empty
                    <li class="px-5 py-8 text-center text-sm text-slate-500">Belum ada pengajuan.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="mt-6">
        <a href="{{ route('admin.pengajuan.index') }}"
            class="rounded-lg bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-700">
            Lihat Semua Pengajuan
        </a>
    </div>
@endsection
