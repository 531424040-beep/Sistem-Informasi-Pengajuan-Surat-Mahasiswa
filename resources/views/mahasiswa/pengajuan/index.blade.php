@extends('layouts.app')

@section('title', 'Riwayat Pengajuan')

@section('content')
    <form method="GET" action="{{ route('mahasiswa.pengajuan.index') }}"
        class="mb-4 flex flex-col gap-2 sm:flex-row">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nomor pengajuan / keperluan..."
            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-sky-500 sm:w-72">
        <select name="status"
            class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-sky-500">
            <option value="">Semua Status</option>
            @foreach ($statuses as $value)
                <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $value }}</option>
            @endforeach
        </select>
        <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
            Filter
        </button>
    </form>

    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-5 py-3">Nomor</th>
                    <th class="px-5 py-3">Jenis Surat</th>
                    <th class="px-5 py-3">Keperluan</th>
                    <th class="px-5 py-3">Tanggal</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($pengajuans as $pengajuan)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-3 font-medium text-slate-800">{{ $pengajuan->nomor_pengajuan }}</td>
                        <td class="px-5 py-3">{{ $pengajuan->jenisSurat->nama_surat }}</td>
                        <td class="px-5 py-3">{{ \Illuminate\Support\Str::limit($pengajuan->keperluan, 50) }}</td>
                        <td class="px-5 py-3">{{ $pengajuan->tanggal_pengajuan->format('d/m/Y') }}</td>
                        <td class="px-5 py-3"><x-status-badge :status="$pengajuan->status->value" /></td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('mahasiswa.pengajuan.show', $pengajuan) }}"
                                class="font-medium text-sky-600 hover:underline">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-slate-500">Belum ada pengajuan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $pengajuans->links() }}
    </div>
@endsection
