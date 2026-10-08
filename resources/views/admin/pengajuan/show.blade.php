@extends('layouts.app')

@section('title', 'Detail & Verifikasi Pengajuan')

@section('content')
    <a href="{{ route('admin.pengajuan.index') }}" class="mb-4 inline-block text-sm text-sky-600 hover:underline">&larr;
        Kembali ke daftar pengajuan</a>

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
                @if ($pengajuan->adminPemroses)
                    <div class="flex justify-between"><dt class="text-slate-500">Diproses oleh</dt>
                        <dd class="font-medium">{{ $pengajuan->adminPemroses->name }}</dd></div>
                @endif
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
                        <p class="text-xs text-slate-500">{{ $dokumen->nama_file }}</p>
                    </div>
                    <a href="{{ route('admin.pengajuan.dokumen', [$pengajuan, $dokumen]) }}"
                        class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                        Lihat / Unduh
                    </a>
                </li>
            @empty
                <li class="py-3 text-sm text-slate-500">Tidak ada dokumen terunggah.</li>
            @endforelse
        </ul>
    </div>

    {{-- Tindakan --}}
    <div class="mt-4 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-sm font-semibold text-slate-800">Tindakan Pemeriksaan</h2>

        <div class="mt-4 flex flex-wrap gap-3">
            @if ($pengajuan->statusIs(\App\Enums\PengajuanStatus::Diajukan))
                <form method="POST" action="{{ route('admin.pengajuan.periksa', $pengajuan) }}"
                    onsubmit="return confirm('Tandai pengajuan ini sedang diperiksa?')">
                    @csrf
                    <button type="submit"
                        class="rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-600">
                        Tandai "Diperiksa"
                    </button>
                </form>
            @endif

            @if ($pengajuan->statusIs(\App\Enums\PengajuanStatus::Diperiksa))
                <form method="POST" action="{{ route('admin.pengajuan.verifikasi', $pengajuan) }}"
                    onsubmit="return confirm('Setujui pengajuan ini dan lanjutkan ke tahap pemrosesan?')">
                    @csrf
                    <input type="hidden" name="action" value="setujui">
                    <button type="submit"
                        class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                        Setujui
                    </button>
                </form>

                <button type="button" onclick="document.getElementById('dialog-perbaikan').showModal()"
                    class="rounded-lg bg-orange-500 px-4 py-2 text-sm font-semibold text-white hover:bg-orange-600">
                    Perlu Perbaikan
                </button>

                <button type="button" onclick="document.getElementById('dialog-tolak').showModal()"
                    class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">
                    Tolak
                </button>
            @endif

            @if ($pengajuan->statusIs(\App\Enums\PengajuanStatus::Diproses))
                <button type="button" onclick="document.getElementById('dialog-selesai').showModal()"
                    class="rounded-lg bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-700">
                    Unggah Surat & Selesaikan
                </button>
            @endif

            @if ($pengajuan->statusIs(\App\Enums\PengajuanStatus::Selesai) && $pengajuan->file_surat)
                <span class="rounded-lg bg-emerald-50 px-4 py-2 text-sm font-medium text-emerald-700">
                    Surat hasil sudah diunggah ({{ $pengajuan->completed_at?->format('d/m/Y H:i') }})
                </span>
            @endif

            @if ($pengajuan->statusIs(\App\Enums\PengajuanStatus::PerluPerbaikan))
                <span class="rounded-lg bg-orange-50 px-4 py-2 text-sm font-medium text-orange-700">
                    Menunggu perbaikan dari mahasiswa
                </span>
            @endif

            @if ($pengajuan->statusIs(\App\Enums\PengajuanStatus::Ditolak))
                <span class="rounded-lg bg-red-50 px-4 py-2 text-sm font-medium text-red-700">
                    Pengajuan telah ditolak
                </span>
            @endif
        </div>

        @if ($pengajuan->catatan_admin)
            <p class="mt-4 rounded-lg bg-slate-50 px-4 py-3 text-sm text-slate-600">
                <span class="font-medium">Catatan terakhir:</span> {{ $pengajuan->catatan_admin }}
            </p>
        @endif
    </div>

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

    {{-- Modal: Perlu Perbaikan --}}
    <dialog id="dialog-perbaikan" class="w-full max-w-md rounded-xl p-0 backdrop:bg-black/50">
        <form method="POST" action="{{ route('admin.pengajuan.verifikasi', $pengajuan) }}" class="bg-white p-6">
            @csrf
            <input type="hidden" name="action" value="perbaikan">
            <h3 class="text-lg font-semibold text-slate-800">Minta Perbaikan</h3>
            <p class="mt-1 text-sm text-slate-500">Wajib memberikan catatan alasan perbaikan.</p>
            <textarea name="catatan" rows="4" required placeholder="Jelaskan bagian yang harus diperbaiki..."
                class="mt-4 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-sky-500"></textarea>
            <div class="mt-4 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('dialog-perbaikan').close()"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">Batal</button>
                <button type="submit"
                    class="rounded-lg bg-orange-500 px-4 py-2 text-sm font-semibold text-white hover:bg-orange-600">Kirim
                    Permintaan</button>
            </div>
        </form>
    </dialog>

    {{-- Modal: Tolak --}}
    <dialog id="dialog-tolak" class="w-full max-w-md rounded-xl p-0 backdrop:bg-black/50">
        <form method="POST" action="{{ route('admin.pengajuan.verifikasi', $pengajuan) }}" class="bg-white p-6">
            @csrf
            <input type="hidden" name="action" value="tolak">
            <h3 class="text-lg font-semibold text-slate-800">Tolak Pengajuan</h3>
            <p class="mt-1 text-sm text-slate-500">Wajib memberikan alasan penolakan.</p>
            <textarea name="catatan" rows="4" required placeholder="Jelaskan alasan penolakan..."
                class="mt-4 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-sky-500"></textarea>
            <div class="mt-4 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('dialog-tolak').close()"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">Batal</button>
                <button type="submit"
                    class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Tolak
                    Pengajuan</button>
            </div>
        </form>
    </dialog>

    {{-- Modal: Selesai + Upload Surat --}}
    <dialog id="dialog-selesai" class="w-full max-w-md rounded-xl p-0 backdrop:bg-black/50">
        <form method="POST" action="{{ route('admin.pengajuan.selesai', $pengajuan) }}" enctype="multipart/form-data"
            class="bg-white p-6">
            @csrf
            <h3 class="text-lg font-semibold text-slate-800">Unggah Surat Hasil</h3>
            <p class="mt-1 text-sm text-slate-500">Unggah file surat dalam format PDF (maksimal 2 MB).</p>
            <input type="file" name="file_surat" accept=".pdf" required
                class="mt-4 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-slate-700 hover:file:bg-slate-200">
            <div class="mt-4 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('dialog-selesai').close()"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">Batal</button>
                <button type="submit"
                    class="rounded-lg bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-700">Unggah &
                    Selesaikan</button>
            </div>
        </form>
    </dialog>
@endsection
