@extends('layouts.app')

@section('title', 'Verifikasi Surat Pengajuan')

@section('content')
    <p class="mb-4 text-sm text-slate-600">Periksa data dan dokumen mahasiswa, lalu ambil keputusan:
        <span class="font-medium text-emerald-700">Setujui</span>,
        <span class="font-medium text-orange-700">Perlu Perbaikan</span>, atau
        <span class="font-medium text-red-700">Tolak</span>.</p>

    <form method="GET" action="{{ route('admin.pengajuan.verifikasi.index') }}" class="mb-4 flex flex-wrap items-end gap-2">
        <div>
            <label class="mb-1 block text-xs text-slate-500">Cari (NIM / nama / nomor)</label>
            <input type="text" name="q" value="{{ request('q') }}"
                class="w-56 rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-sky-500">
        </div>
        <div>
            <label class="mb-1 block text-xs text-slate-500">Status</label>
            <select name="status" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-sky-500">
                <option value="">Menunggu Verifikasi</option>
                <option value="Diajukan" {{ request('status') === 'Diajukan' ? 'selected' : '' }}>Diajukan</option>
                <option value="Diperiksa" {{ request('status') === 'Diperiksa' ? 'selected' : '' }}>Diperiksa</option>
            </select>
        </div>
        <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
            Filter
        </button>
    </form>

    <div class="space-y-4">
        @forelse ($pengajuans as $pengajuan)
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                    {{-- Info pengajuan --}}
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="text-sm font-semibold text-slate-800">{{ $pengajuan->nomor_pengajuan }}</p>
                            <x-status-badge :status="$pengajuan->status->value" />
                        </div>
                        <p class="mt-1 text-sm text-slate-700">
                            {{ $pengajuan->user->name }}
                            <span class="text-slate-400">({{ $pengajuan->user->nim ?: '-' }})</span>
                            — {{ $pengajuan->jenisSurat->nama_surat }}
                        </p>
                        <p class="mt-1 text-sm text-slate-600">
                            <span class="text-slate-500">Keperluan:</span> {{ $pengajuan->keperluan }}
                        </p>
                        <p class="mt-1 text-xs text-slate-500">
                            Diajukan {{ $pengajuan->tanggal_pengajuan->format('d/m/Y') }} ·
                            {{ $pengajuan->dokumenPengajuans->count() }} dokumen terunggah
                        </p>
                    </div>

                    {{-- Aksi --}}
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('admin.pengajuan.show', $pengajuan) }}"
                            class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                            Periksa Dokumen
                        </a>

                        @if ($pengajuan->statusIs(\App\Enums\PengajuanStatus::Diajukan))
                            <form method="POST" action="{{ route('admin.pengajuan.periksa', $pengajuan) }}"
                                onsubmit="return confirm('Tandai pengajuan ini sedang diperiksa?')">
                                @csrf
                                <button type="submit"
                                    class="rounded-lg bg-amber-500 px-3 py-2 text-sm font-semibold text-white hover:bg-amber-600">
                                    Tandai Diperiksa
                                </button>
                            </form>
                        @endif

                        @if ($pengajuan->statusIs(\App\Enums\PengajuanStatus::Diperiksa))
                            <form method="POST" action="{{ route('admin.pengajuan.verifikasi', $pengajuan) }}"
                                onsubmit="return confirm('Setujui pengajuan ini dan lanjutkan ke tahap pemrosesan?')">
                                @csrf
                                <input type="hidden" name="action" value="setujui">
                                <button type="submit"
                                    class="rounded-lg bg-emerald-600 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                                    Setujui
                                </button>
                            </form>

                            <button type="button" onclick="document.getElementById('dialog-perbaikan-{{ $pengajuan->id }}').showModal()"
                                class="rounded-lg bg-orange-500 px-3 py-2 text-sm font-semibold text-white hover:bg-orange-600">
                                Perlu Perbaikan
                            </button>

                            <button type="button" onclick="document.getElementById('dialog-tolak-{{ $pengajuan->id }}').showModal()"
                                class="rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700">
                                Tolak
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-slate-200 bg-white p-8 text-center text-sm text-slate-500">
                Tidak ada pengajuan yang menunggu verifikasi. 🎉
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $pengajuans->links() }}
    </div>

    {{-- Modal: Perlu Perbaikan --}}
    @foreach ($pengajuans as $pengajuan)
        <dialog id="dialog-perbaikan-{{ $pengajuan->id }}" class="w-full max-w-md rounded-xl p-0 backdrop:bg-black/50">
            <form method="POST" action="{{ route('admin.pengajuan.verifikasi', $pengajuan) }}" class="bg-white p-6">
                @csrf
                <input type="hidden" name="action" value="perbaikan">
                <h3 class="text-lg font-semibold text-slate-800">Minta Perbaikan</h3>
                <p class="mt-1 text-sm text-slate-500">{{ $pengajuan->nomor_pengajuan }} —
                    {{ $pengajuan->user->name }}. Wajib memberikan catatan alasan perbaikan.</p>
                <textarea name="catatan" rows="4" required placeholder="Jelaskan bagian yang harus diperbaiki..."
                    class="mt-4 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-sky-500"></textarea>
                <div class="mt-4 flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('dialog-perbaikan-{{ $pengajuan->id }}').close()"
                        class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="submit"
                        class="rounded-lg bg-orange-500 px-4 py-2 text-sm font-semibold text-white hover:bg-orange-600">Kirim
                        Permintaan</button>
                </div>
            </form>
        </dialog>

        {{-- Modal: Tolak --}}
        <dialog id="dialog-tolak-{{ $pengajuan->id }}" class="w-full max-w-md rounded-xl p-0 backdrop:bg-black/50">
            <form method="POST" action="{{ route('admin.pengajuan.verifikasi', $pengajuan) }}" class="bg-white p-6">
                @csrf
                <input type="hidden" name="action" value="tolak">
                <h3 class="text-lg font-semibold text-slate-800">Tolak Pengajuan</h3>
                <p class="mt-1 text-sm text-slate-500">{{ $pengajuan->nomor_pengajuan }} —
                    {{ $pengajuan->user->name }}. Wajib memberikan alasan penolakan.</p>
                <textarea name="catatan" rows="4" required placeholder="Jelaskan alasan penolakan..."
                    class="mt-4 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-sky-500"></textarea>
                <div class="mt-4 flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('dialog-tolak-{{ $pengajuan->id }}').close()"
                        class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="submit"
                        class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Tolak
                        Pengajuan</button>
                </div>
            </form>
        </dialog>
    @endforeach
@endsection
