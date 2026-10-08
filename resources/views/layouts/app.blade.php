<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-800 antialiased">
    <div class="flex min-h-screen">

        {{-- Sidebar --}}
        <aside id="sidebar"
            class="fixed inset-y-0 left-0 z-40 w-64 -translate-x-full border-r border-slate-800 bg-slate-900 text-slate-100 transition-transform md:static md:translate-x-0">
            <div class="flex h-16 items-center gap-2 border-b border-slate-800 px-6">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-sky-600 text-sm font-bold">S</span>
                <div class="leading-tight">
                    <p class="text-sm font-semibold">SIAKAD Surat</p>
                    <p class="text-xs text-slate-400">Pengajuan Surat Online</p>
                </div>
            </div>

            <nav class="space-y-1 p-4 text-sm">
                @auth
                    @if (auth()->user()->isAdmin())
                        <p class="px-3 pb-2 pt-1 text-xs font-semibold uppercase tracking-wider text-slate-500">Menu Admin</p>
                        <a href="{{ route('admin.dashboard') }}"
                            class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('admin.dashboard') ? 'bg-sky-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('admin.pengajuan.index') }}"
                            class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('admin.pengajuan.*') && ! request()->routeIs('admin.pengajuan.verifikasi.index') ? 'bg-sky-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                            Pengajuan Surat
                        </a>
                        <a href="{{ route('admin.pengajuan.verifikasi.index') }}"
                            class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('admin.pengajuan.verifikasi.index') ? 'bg-sky-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                            Verifikasi Surat
                        </a>
                        <a href="{{ route('admin.jenis-surat.index') }}"
                            class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('admin.jenis-surat.*') ? 'bg-sky-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                            Jenis Surat
                        </a>
                        <a href="{{ route('admin.laporan') }}"
                            class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('admin.laporan') ? 'bg-sky-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                            Laporan
                        </a>
                    @else
                        <p class="px-3 pb-2 pt-1 text-xs font-semibold uppercase tracking-wider text-slate-500">Menu Mahasiswa</p>
                        <a href="{{ route('mahasiswa.dashboard') }}"
                            class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('mahasiswa.dashboard') ? 'bg-sky-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('mahasiswa.surat.index') }}"
                            class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('mahasiswa.surat.*') || request()->routeIs('mahasiswa.pengajuan.create') ? 'bg-sky-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                            Ajukan Surat
                        </a>
                        <a href="{{ route('mahasiswa.pengajuan.index') }}"
                            class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('mahasiswa.pengajuan.index') ? 'bg-sky-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                            Riwayat Pengajuan
                        </a>
                        <a href="{{ route('mahasiswa.profile') }}"
                            class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('mahasiswa.profile') ? 'bg-sky-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                            Profil
                        </a>
                    @endif
                @endauth
            </nav>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">

            {{-- Navbar --}}
            <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-slate-200 bg-white px-4 md:px-6">
                <div class="flex items-center gap-3">
                    <button type="button" onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')"
                        class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 md:hidden" aria-label="Buka menu">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h1 class="text-sm font-semibold text-slate-700 md:text-base">@yield('title', 'Dashboard')</h1>
                </div>

                @auth
                    <div class="flex items-center gap-3">
                        <div class="hidden text-right sm:block">
                            <p class="text-sm font-medium text-slate-800">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-500">
                                {{ auth()->user()->isAdmin() ? 'Admin / Petugas' : 'Mahasiswa' . (auth()->user()->nim ? ' — ' . auth()->user()->nim : '') }}
                            </p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm font-medium text-slate-600 hover:bg-slate-50">
                                Logout
                            </button>
                        </form>
                    </div>
                @endauth
            </header>

            {{-- Konten --}}
            <main class="flex-1 p-4 md:p-6">
                @if (session('success'))
                    <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>

</html>
