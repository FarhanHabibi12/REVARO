<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Masuk') — REVARO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.47.0/dist/tabler-icons.min.css">
    <style>body { font-family: 'Segoe UI', system-ui, sans-serif; }</style>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center px-4">

    <div class="w-full max-w-sm">

        <div class="flex items-center justify-center gap-2 mb-6">
            <div class="w-9 h-9 rounded-lg bg-teal-500 flex items-center justify-center font-bold text-slate-900">R</div>
            <span class="font-semibold text-slate-900 text-xl tracking-tight">REVARO</span>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            @yield('content')
        </div>

        <p class="text-center text-xs text-slate-400 mt-6">&copy; {{ date('Y') }} REVARO. Panel internal admin.</p>
    </div>

</body>
</html>
