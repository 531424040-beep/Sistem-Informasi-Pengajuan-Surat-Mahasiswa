@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
    <div class="max-w-xl rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-800">Profil Mahasiswa</h2>

        <dl class="mt-4 space-y-3 text-sm">
            <div class="flex justify-between border-b border-slate-100 pb-2">
                <dt class="text-slate-500">Nama</dt>
                <dd class="font-medium text-slate-800">{{ $user->name }}</dd>
            </div>
            <div class="flex justify-between border-b border-slate-100 pb-2">
                <dt class="text-slate-500">NIM</dt>
                <dd class="font-medium text-slate-800">{{ $user->nim ?: '-' }}</dd>
            </div>
            <div class="flex justify-between border-b border-slate-100 pb-2">
                <dt class="text-slate-500">Email</dt>
                <dd class="font-medium text-slate-800">{{ $user->email }}</dd>
            </div>
            <div class="flex justify-between border-b border-slate-100 pb-2">
                <dt class="text-slate-500">Program Studi</dt>
                <dd class="font-medium text-slate-800">{{ $user->program_studi ?: '-' }}</dd>
            </div>
            <div class="flex justify-between border-b border-slate-100 pb-2">
                <dt class="text-slate-500">Fakultas</dt>
                <dd class="font-medium text-slate-800">{{ $user->fakultas ?: '-' }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-slate-500">Terdaftar sejak</dt>
                <dd class="font-medium text-slate-800">{{ $user->created_at->format('d/m/Y') }}</dd>
            </div>
        </dl>

        <p class="mt-6 rounded-lg bg-slate-50 px-4 py-3 text-xs text-slate-500">
            Perubahan data profil (NIM, prodi, fakultas) hanya dapat dilakukan oleh Admin akademik.
        </p>
    </div>
@endsection
