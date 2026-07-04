<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Dompetku') }} - @yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="h-full bg-slate-50 text-slate-800 dark:bg-slate-950 dark:text-slate-200 transition-colors duration-200" x-data="{ sidebarOpen: false }">
    <div class="flex h-full pb-16 lg:pb-0">
        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/50 z-40 lg:hidden" @click="sidebarOpen = false"></div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 flex flex-col">
            <div class="flex items-center gap-3 px-6 py-5 border-b border-slate-100 dark:border-slate-800">
                <img src="{{ asset('images/logo.png') }}" alt="Dompetku Logo" class="w-9 h-9 rounded-xl object-cover">
                <span class="font-bold text-lg text-slate-900 dark:text-white">Dompetku</span>
            </div>
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto scrollbar-hide">
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 hover:text-slate-900 dark:hover:bg-slate-800/40 dark:hover:text-slate-200' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                    Dashboard
                </a>
                <a href="{{ route('transactions.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('transactions.*') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 hover:text-slate-900 dark:hover:bg-slate-800/40 dark:hover:text-slate-200' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" /></svg>
                    Transactions
                </a>
                <a href="{{ route('categories.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('categories.*') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 hover:text-slate-900 dark:hover:bg-slate-800/40 dark:hover:text-slate-200' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                    Categories
                </a>
                <a href="{{ route('tags.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('tags.*') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 hover:text-slate-900 dark:hover:bg-slate-800/40 dark:hover:text-slate-200' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    Tags
                </a>
                <a href="{{ route('accounts.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('accounts.*') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 hover:text-slate-900 dark:hover:bg-slate-800/40 dark:hover:text-slate-200' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                    Accounts
                </a>
                <a href="{{ route('assets.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('assets.*') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 hover:text-slate-900 dark:hover:bg-slate-800/40 dark:hover:text-slate-200' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                    Assets
                </a>
                <a href="{{ route('budgets.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('budgets.*') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 hover:text-slate-900 dark:hover:bg-slate-800/40 dark:hover:text-slate-200' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Budgets
                </a>
                <a href="{{ route('savings-goals.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('savings-goals.*') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 hover:text-slate-900 dark:hover:bg-slate-800/40 dark:hover:text-slate-200' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Savings Goals
                </a>
                <a href="{{ route('recurring.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('recurring.*') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 hover:text-slate-900 dark:hover:bg-slate-800/40 dark:hover:text-slate-200' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    Recurring
                </a>
                <a href="{{ route('settings.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('settings.*') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 hover:text-slate-900 dark:hover:bg-slate-800/40 dark:hover:text-slate-200' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Settings
                </a>
                <a href="{{ route('reports.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('reports.*') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 hover:text-slate-900 dark:hover:bg-slate-800/40 dark:hover:text-slate-200' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    Reports
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-auto">
            <!-- Top Bar -->
            <header class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 sticky top-0 z-30">
                <div class="px-4 sm:px-6 lg:px-8 py-3 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 -ml-2 text-slate-500 hover:text-slate-700 rounded-lg hover:bg-slate-100 dark:text-slate-400 dark:hover:text-slate-200 dark:hover:bg-slate-800">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                        </button>
                        <h1 class="text-lg font-semibold text-slate-800 dark:text-white">@yield('title', 'Dashboard')</h1>
                    </div>
                    <div class="flex items-center gap-4">
                        @yield('actions')
                        <div class="h-6 w-px bg-slate-200 dark:bg-slate-800"></div>

                        <!-- Dark Mode Toggle -->
                        <div x-data="{
                            darkMode: localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
                            toggleTheme() {
                                this.darkMode = !this.darkMode;
                                if (this.darkMode) {
                                    document.documentElement.classList.add('dark');
                                    localStorage.setItem('theme', 'dark');
                                } else {
                                    document.documentElement.classList.remove('dark');
                                    localStorage.setItem('theme', 'light');
                                }
                            }
                        }">
                            <button @click="toggleTheme()" class="p-2 text-slate-500 hover:text-indigo-600 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition dark:text-slate-400 dark:hover:text-indigo-400" title="Toggle Dark/Light Mode">
                                <svg x-show="!darkMode" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                </svg>
                                <svg x-show="darkMode" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m2.828 0l-.707-.707m12.022-12.022l-.707.707M12 7a5 5 0 100 10 5 5 0 000-10z" />
                                </svg>
                            </button>
                        </div>

                        <div class="h-6 w-px bg-slate-200 dark:bg-slate-800"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-sm font-medium text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/20 dark:text-rose-400 dark:hover:bg-rose-950/40 dark:border-rose-900/50 px-3 py-1.5 rounded-lg transition tracking-wide shadow-sm border border-rose-100/50">Logout</button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            @if(session('success'))
            <div class="mx-4 sm:mx-6 lg:mx-8 mt-4" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                 x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 dark:bg-emerald-950/20 dark:border-emerald-900/50 dark:text-emerald-400 px-4 py-3 rounded-xl text-sm flex items-center justify-between">
                    <span>{{ session('success') }}</span>
                    <button @click="show = false" class="text-emerald-500 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300">×</button>
                </div>
            </div>
            @endif

            <main class="flex-1 p-4 sm:p-6 lg:p-8 pb-24 lg:pb-8">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Mobile Bottom Nav Bar -->
    <nav class="lg:hidden fixed bottom-0 inset-x-0 bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 z-40 flex justify-around items-center py-2 px-1 shadow-[0_-4px_12px_rgba(0,0,0,0.03)] dark:shadow-[0_-4px_12px_rgba(0,0,0,0.3)]">
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-0.5 text-[10px] font-semibold {{ request()->routeIs('dashboard') ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400' }}">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('transactions.index') }}" class="flex flex-col items-center gap-0.5 text-[10px] font-semibold {{ request()->routeIs('transactions.*') ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400' }}">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" /></svg>
            <span>Transactions</span>
        </a>
        <a href="{{ route('transactions.create') }}" class="flex flex-col items-center gap-0.5 text-[10px] font-semibold text-slate-500">
            <div class="bg-indigo-600 dark:bg-indigo-500 text-white p-3 rounded-full -mt-6 shadow-lg shadow-indigo-600/30 border-4 border-slate-50 dark:border-slate-950 transition hover:scale-105 active:scale-95">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" /></svg>
            </div>
        </a>
        <a href="{{ route('categories.index') }}" class="flex flex-col items-center gap-0.5 text-[10px] font-semibold {{ request()->routeIs('categories.*') ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400' }}">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
            <span>Categories</span>
        </a>
        <a href="{{ route('settings.index') }}" class="flex flex-col items-center gap-0.5 text-[10px] font-semibold {{ request()->routeIs('settings.*') ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400' }}">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            <span>Settings</span>
        </a>
    </nav>

    <!-- Global Confirmation Modal -->
    <div x-data="{ 
            isOpen: false, 
            message: '', 
            action: '', 
            openModal(e, formAction, confirmMsg) {
                e.preventDefault();
                this.action = formAction;
                this.message = confirmMsg;
                this.isOpen = true;
            }
        }" 
        @open-confirm-modal.window="openModal($event, $event.detail.action, $event.detail.message)">
        
        <div x-show="isOpen" 
             x-transition.opacity 
             class="fixed inset-0 z-[100] bg-slate-900/50 backdrop-blur-sm"
             style="display: none;"></div>

        <div x-show="isOpen" 
             x-transition 
             class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6"
             style="display: none;">
            <div @click.away="isOpen = false" class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-100 dark:border-slate-800 p-6 sm:p-8 w-full max-w-md transform transition-all">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 rounded-full bg-rose-50 dark:bg-rose-950/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-800 dark:text-white">Confirm Deletion</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 leading-relaxed" x-text="message"></p>
                    </div>
                </div>
                <div class="flex gap-3 justify-end">
                    <button @click="isOpen = false" type="button" class="px-5 py-2.5 text-sm font-semibold text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl transition">Cancel</button>
                    <form :action="action" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition shadow-sm">Yes, delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
