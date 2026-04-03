<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Dompetku') }} - Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-indigo-50/50 text-slate-800 antialiased min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center mb-5 shrink-0">
                <img src="{{ asset('images/logo.png') }}" alt="Dompetku Logo" class="w-16 h-16 rounded-3xl object-cover shadow-xl shadow-indigo-600/10">
            </div>
            <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">Dompetku</h1>
            <p class="text-slate-500 font-bold mt-2 tracking-wide">Secure Financial Tracking</p>
        </div>
        
        <!-- Card -->
        <div class="bg-white p-8 sm:p-10 rounded-3xl shadow-[0_20px_50px_-12px_rgba(0,0,0,0.05)] border border-slate-100/50 overflow-hidden relative">
            <!-- Decorative gradient -->
            <div class="absolute inset-x-0 -top-px h-1 bg-gradient-to-r from-transparent via-indigo-500 to-transparent opacity-50"></div>
            {{ $slot }}
        </div>
        
        <p class="text-center text-slate-400 text-sm font-semibold mt-8 tracking-wider uppercase text-[11px]">Private Tracker • v2.0</p>
    </div>
</body>
</html>
