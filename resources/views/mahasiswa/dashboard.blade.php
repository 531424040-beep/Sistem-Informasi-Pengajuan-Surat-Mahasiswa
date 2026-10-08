@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa')

@section('content')
    <p class="mb-6 text-sm text-slate-600">Selamat datang, <span class="font-semibold">{{ auth()->user()->name }}</span></p>

    <div class="mb-6 flex flex-col gap-3 sm:flex-row">
        <a href="{{ route('mahasiswa.surat.index') }}"
            class="rounded-lg bg-sky-600 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-sky-700">
            Ajukan Surat
        </a>
        <a href="{{ route('mahasiswa.pengajuan.index') }}"
            class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">
            Riwayat Pengajuan
        </a>
    </div>

    {{-- Kartu statistik --}}
    <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-5">
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs text-slate-500">Total Pengajuan</p>
            <p class="mt-1 text-2xl font-bold text-slate-800">{{ $stats['total'] }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs text-slate-500">Sedang Diproses</p>
            <p class="mt-1 text-2xl font-bold text-indigo-600">{{ $stats['diproses'] }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs text-slate-500">Perlu Perbaikan</p>
            <p class="mt-1 text-2xl font-bold text-orange-600">{{ $stats['perlu_perbaikan'] }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs text-slate-500">Surat Selesai</p>
            <p class="mt-1 text-2xl font-bold text-emerald-600">{{ $stats['selesai'] }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs text-slate-500">Ditolak</p>
            <p class="mt-1 text-2xl font-bold text-red-600">{{ $stats['ditolak'] }}</p>
        </div>
    </div>

    {{-- Pengajuan terbaru --}}
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-5 py-4">
            <h2 class="text-sm font-semibold text-slate-800">Pengajuan Terbaru</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Nomor Pengajuan</th>
                        <th class="px-5 py-3">Jenis Surat</th>
                        <th class="px-5 py-3">Tanggal</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($pengajuanTerbaru as $pengajuan)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-3 font-medium text-slate-800">{{ $pengajuan->nomor_pengajuan }}</td>
                            <td class="px-5 py-3">{{ $pengajuan->jenisSurat->nama_surat }}</td>
                            <td class="px-5 py-3">{{ $pengajuan->tanggal_pengajuan->format('d/m/Y') }}</td>
                            <td class="px-5 py-3"><x-status-badge :status="$pengajuan->status->value" /></td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('mahasiswa.pengajuan.show', $pengajuan) }}"
                                    class="text-sm font-medium text-sky-600 hover:underline">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-slate-500">
                                Belum ada pengajuan. Mulai dengan menekan tombol "Ajukan Surat".
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
