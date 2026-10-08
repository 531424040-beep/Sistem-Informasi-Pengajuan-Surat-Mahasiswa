@extends('layouts.app')

@section('title', 'Detail Pengajuan')

@section('content')
    <a href="{{ route('mahasiswa.pengajuan.index') }}" class="mb-4 inline-block text-sm text-sky-600 hover:underline">&larr;
        Kembali ke riwayat</a>

    {{-- Catatan Admin --}}
    @if ($pengajuan->catatan_admin)
        <div
            class="mb-4 rounded-lg border border-orange-200 bg-orange-50 px-4 py-3 text-sm text-orange-800">
            <p class="font-semibold">Catatan Admin:</p>
            <p class="mt-1">{{ $pengajuan->catatan_admin }}</p>
        </div>
    @endif

    {{-- Alasan penolakan --}}
    @if ($pengajuan->statusIs(\App\Enums\PengajuanStatus::Ditolak) && $pengajuan->catatan_admin)
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <p class="font-semibold">Alasan Penolakan:</p>
            <p class="mt-1">{{ $pengajuan->catatan_admin }}</p>
        </div>
    @endif

    <div class="grid gap-4 lg:grid-cols-2">
        {{-- Data Mahasiswa --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-800">Data Mahasiswa</h2>
            <dl class="mt-3 space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-slate-500">Nama</dt>
                    <dd class="font-medium">{{ $pengajuan->user->name }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">NIM</dt>
                    <dd class="font-medium">{{ $pengajuan->user->nim ?: '-' }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Program Studi</dt>
                    <dd class="font-medium">{{ $pengajuan->user->program_studi ?: '-' }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Fakultas</dt>
                    <dd class="font-medium">{{ $pengajuan->user->fakultas ?: '-' }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Email</dt>
                    <dd class="font-medium">{{ $pengajuan->user->email }}</dd></div>
            </dl>
        </div>

        {{-- Data Pengajuan --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-800">Data Pengajuan</h2>
            <dl class="mt-3 space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-slate-500">Nomor Pengajuan</dt>
                    <dd class="font-medium">{{ $pengajuan->nomor_pengajuan }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Jenis Surat</dt>
                    <dd class="font-medium">{{ $pengajuan->jenisSurat->nama_surat }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Tanggal Pengajuan</dt>
                    <dd class="font-medium">{{ $pengajuan->tanggal_pengajuan->format('d/m/Y') }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Status</dt>
                    <dd><x-status-badge :status="$pengajuan->status->value" /></dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">Keperluan</dt>
                    <dd class="text-right font-medium">{{ $pengajuan->keperluan }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">Keterangan</dt>
                    <dd class="text-right font-medium">{{ $pengajuan->keterangan ?: '-' }}</dd></div>
            </dl>

            @if ($pengajuan->jenisSurat->nama_surat === 'Surat Pengantar Penelitian')
                <div class="mt-4 space-y-2 border-t border-slate-100 pt-4 text-sm">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Data Penelitian</p>
                    <div class="flex justify-between gap-4"><span class="text-slate-500">Judul</span>
                        <span class="text-right font-medium">{{ $pengajuan->judul_penelitian ?: '-' }}</span></div>
                    <div class="flex justify-between gap-4"><span class="text-slate-500">Instansi Tujuan</span>
                        <span class="text-right font-medium">{{ $pengajuan->instansi_tujuan ?: '-' }}</span></div>
                    <div class="flex justify-between gap-4"><span class="text-slate-500">Alamat Instansi</span>
                        <span class="text-right font-medium">{{ $pengajuan->alamat_instansi ?: '-' }}</span></div>
                    <div class="flex justify-between gap-4"><span class="text-slate-500">Tujuan</span>
                        <span class="text-right font-medium">{{ $pengajuan->tujuan_penelitian ?: '-' }}</span></div>
                </div>
            @endif
        </div>
    </div>

    {{-- Dokumen --}}
    <div class="mt-4 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-sm font-semibold text-slate-800">Dokumen Persyaratan</h2>
        <ul class="mt-3 divide-y divide-slate-100">
            @forelse ($pengajuan->dokumenPengajuans as $dokumen)
                <li class="flex flex-col gap-2 py-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-800">{{ $dokumen->persyaratan?->nama_persyaratan }}</p>
                        <p class="text-xs text-slate-500">{{ $dokumen->nama_file }} ·
                            <span class="text-emerald-600">Terunggah</span></p>
                    </div>
                    <a href="{{ route('mahasiswa.pengajuan.dokumen', [$pengajuan, $dokumen]) }}"
                        class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                        Lihat / Unduh
                    </a>
                </li>
            @empty
                <li class="py-3 text-sm text-slate-500">Belum ada dokumen terunggah.</li>
            @endforelse
        </ul>
    </div>

    {{-- Surat hasil --}}
    @if ($pengajuan->statusIs(\App\Enums\PengajuanStatus::Selesai) && $pengajuan->file_surat)
        <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 p-5">
            <h2 class="text-sm font-semibold text-emerald-800">Surat Selesai</h2>
            <p class="mt-1 text-sm text-emerald-700">Surat Anda sudah selesai diproses dan dapat diunduh.</p>
            <a href="{{ route('mahasiswa.pengajuan.surat.download', $pengajuan) }}"
                class="mt-3 inline-block rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                Unduh Surat (PDF)
            </a>
        </div>
    @endif

    {{-- Riwayat Status --}}
    <div class="mt-4 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-sm font-semibold text-slate-800">Riwayat Status</h2>
        <ol class="mt-4 space-y-4 border-l border-slate-200 pl-5">
            @foreach ($pengajuan->riwayatStatuses as $riwayat)
                <li class="relative">
                    <span class="absolute -left-[26px] top-1.5 h-3 w-3 rounded-full border-2 border-white bg-sky-600"></span>
                    <p class="text-sm font-medium text-slate-800">
                        {{ $riwayat->status }} —
                        <span class="font-normal text-slate-500">{{ $riwayat->created_at->format('d/m/Y H:i') }}</span>
                    </p>
                    <p class="text-sm text-slate-600">{{ $riwayat->keterangan ?: '-' }}</p>
                    <p class="text-xs text-slate-400">oleh: {{ $riwayat->user?->name }}</p>
                </li>
            @endforeach
        </ol>
    </div>

    @if ($pengajuan->canBeEdited())
        <div class="mt-4">
            <a href="{{ route('mahasiswa.pengajuan.edit', $pengajuan) }}"
                class="inline-block rounded-lg bg-orange-600 px-4 py-2 text-sm font-semibold text-white hover:bg-orange-700">
                Perbaiki Pengajuan
            </a>
        </div>
    @endif
@endsection
