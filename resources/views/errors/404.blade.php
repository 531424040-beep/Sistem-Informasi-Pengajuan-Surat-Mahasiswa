<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css'])
</head>

<body class="flex min-h-screen items-center justify-center bg-slate-100 px-4">
    <div class="max-w-md rounded-2xl bg-white p-10 text-center shadow-lg">
        <p class="text-5xl font-bold text-sky-600">404</p>
        <h1 class="mt-3 text-lg font-semibold text-slate-800">Halaman tidak ditemukan</h1>
        <p class="mt-2 text-sm text-slate-500">Halaman atau data yang Anda cari tidak tersedia.</p>
        <a href="{{ url('/') }}"
            class="mt-6 inline-block rounded-lg bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-700">
            Kembali ke Beranda
        </a>
    </div>
</body>

</html>
