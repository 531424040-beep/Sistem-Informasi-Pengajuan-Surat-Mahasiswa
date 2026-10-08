@extends('layouts.app')

@section('title', 'Formulir Pengajuan Surat')

@section('content')
    <a href="{{ route('mahasiswa.surat.index') }}" class="mb-4 inline-block text-sm text-sky-600 hover:underline">&larr;
        Kembali ke daftar surat</a>

    <div class="max-w-3xl rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-800">Formulir Pengajuan: {{ $jenisSurat->nama_surat }}</h2>
        <p class="mt-1 text-sm text-slate-500">Isi formulir berikut dan unggah dokumen persyaratan.</p>

        @if ($errors->any())
            <div class="mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <p class="font-medium">Terjadi kesalahan pada pengisian formulir:</p>
                <ul class="mt-1 list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('mahasiswa.pengajuan.store') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
            @csrf
            <input type="hidden" name="jenis_surat_id" value="{{ $jenisSurat->id }}">

            {{-- Data mahasiswa (otomatis dari akun login) --}}
            <div class="grid gap-4 rounded-lg bg-slate-50 p-4 sm:grid-cols-2">
                <div>
                    <label class="text-xs font-medium text-slate-500">Nama Mahasiswa</label>
                    <p class="text-sm font-medium text-slate-800">{{ $user->name }}</p>
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500">NIM</label>
                    <p class="text-sm font-medium text-slate-800">{{ $user->nim ?: '-' }}</p>
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500">Email</label>
                    <p class="text-sm font-medium text-slate-800">{{ $user->email }}</p>
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500">Program Studi</label>
                    <p class="text-sm font-medium text-slate-800">{{ $user->program_studi ?: '-' }}</p>
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500">Fakultas</label>
                    <p class="text-sm font-medium text-slate-800">{{ $user->fakultas ?: '-' }}</p>
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500">Tanggal Pengajuan</label>
                    <p class="text-sm font-medium text-slate-800">{{ now()->format('d/m/Y') }}</p>
                </div>
            </div>

            {{-- Data pengajuan --}}
            <div>
                <label for="keperluan" class="mb-1 block text-sm font-medium text-slate-700">Keperluan Surat <span
                        class="text-red-500">*</span></label>
                <input type="text" id="keperluan" name="keperluan" value="{{ old('keperluan') }}" required
                    placeholder="Contoh: Persyaratan pendaftaran beasiswa"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-sky-500">
            </div>

            @if ($jenisSurat->nama_surat === 'Surat Pengantar Penelitian')
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="judul_penelitian" class="mb-1 block text-sm font-medium text-slate-700">Judul
                            Penelitian <span class="text-red-500">*</span></label>
                        <input type="text" id="judul_penelitian" name="judul_penelitian"
                            value="{{ old('judul_penelitian') }}"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-sky-500">
                    </div>
                    <div>
                        <label for="instansi_tujuan" class="mb-1 block text-sm font-medium text-slate-700">Instansi
                            Tujuan <span class="text-red-500">*</span></label>
                        <input type="text" id="instansi_tujuan" name="instansi_tujuan"
                            value="{{ old('instansi_tujuan') }}"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-sky-500">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="alamat_instansi" class="mb-1 block text-sm font-medium text-slate-700">Alamat
                            Instansi <span class="text-red-500">*</span></label>
                        <input type="text" id="alamat_instansi" name="alamat_instansi" value="{{ old('alamat_instansi') }}"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-sky-500">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="tujuan_penelitian" class="mb-1 block text-sm font-medium text-slate-700">Tujuan
                            Penelitian <span class="text-red-500">*</span></label>
                        <textarea id="tujuan_penelitian" name="tujuan_penelitian" rows="3"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-sky-500">{{ old('tujuan_penelitian') }}</textarea>
                    </div>
                </div>
            @endif

            <div>
                <label for="keterangan" class="mb-1 block text-sm font-medium text-slate-700">Keterangan Tambahan</label>
                <textarea id="keterangan" name="keterangan" rows="3"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-sky-500">{{ old('keterangan') }}</textarea>
            </div>

            {{-- Dokumen persyaratan --}}
            <div>
                <h3 class="text-sm font-semibold text-slate-800">Unggah Dokumen Persyaratan</h3>
                <p class="mt-1 text-xs text-slate-500">Format: PDF, JPG, JPEG, PNG. Ukuran maksimal 2 MB per file.</p>

                <div class="mt-3 space-y-4">
                    @foreach ($jenisSurat->persyaratans as $persyaratan)
                        <div>
                            <label for="dokumen-{{ $persyaratan->id }}" class="mb-1 block text-sm font-medium text-slate-700">
                                {{ $persyaratan->nama_persyaratan }}
                                @if ($persyaratan->wajib)
                                    <span class="text-red-500">*</span>
                                @else
                                    <span class="text-xs font-normal text-slate-400">(opsional)</span>
                                @endif
                            </label>
                            <input type="file" id="dokumen-{{ $persyaratan->id }}"
                                name="dokumen[{{ $persyaratan->id }}]" accept=".pdf,.jpg,.jpeg,.png"
                                {{ $persyaratan->wajib ? 'required' : '' }}
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-slate-700 hover:file:bg-slate-200">
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="rounded-lg bg-sky-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-sky-700">
                    Kirim Pengajuan
                </button>
                <a href="{{ route('mahasiswa.surat.index') }}"
                    class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection
