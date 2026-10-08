<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrasi Mahasiswa — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="flex min-h-screen items-center justify-center bg-slate-900 px-4 py-8">
    <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-xl">
        <div class="mb-6 text-center">
            <span class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-sky-600 text-lg font-bold text-white">S</span>
            <h1 class="text-xl font-semibold text-slate-800">Registrasi Mahasiswa</h1>
            <p class="mt-1 text-sm text-slate-500">Buat akun untuk mengajukan surat secara online</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register.store') }}" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="mb-1 block text-sm font-medium text-slate-700">Nama Lengkap</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                    placeholder="Contoh: Budi Santoso"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-sky-500">
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="nim" class="mb-1 block text-sm font-medium text-slate-700">NIM</label>
                    <input type="text" id="nim" name="nim" value="{{ old('nim') }}" required inputmode="numeric"
                        placeholder="Contoh: 20210001"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-sky-500">
                </div>
                <div>
                    <label for="username" class="mb-1 block text-sm font-medium text-slate-700">Username</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" required
                        placeholder="Contoh: budi.s"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-sky-500">
                </div>
            </div>

            <div>
                <label for="email" class="mb-1 block text-sm font-medium text-slate-700">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                    placeholder="nama@mahasiswa.ac.id"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-sky-500">
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="password" class="mb-1 block text-sm font-medium text-slate-700">Password</label>
                    <input type="password" id="password" name="password" required minlength="8"
                        placeholder="Minimal 8 karakter"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-sky-500">
                </div>
                <div>
                    <label for="password_confirmation" class="mb-1 block text-sm font-medium text-slate-700">Konfirmasi
                        Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        placeholder="Ulangi password"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-sky-500">
                </div>
            </div>

            <button type="submit"
                class="w-full rounded-lg bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-sky-700">
                Daftar
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-500">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-medium text-sky-600 hover:underline">Login</a>
        </p>

        <p class="mt-6 text-center text-xs text-slate-400">
            Sistem Informasi Pengajuan Surat Mahasiswa
        </p>
    </div>
</body>

</html>
