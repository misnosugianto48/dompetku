<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4 text-emerald-600 font-medium text-sm text-center" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Email Address</label>
            <input id="email" class="block w-full rounded-2xl border-slate-200 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm px-4 py-3.5 bg-slate-50 transition" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="you@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-rose-500 text-sm font-bold" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-2">
                <label for="password" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Password</label>
            </div>
            <input id="password" class="block w-full rounded-2xl border-slate-200 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm px-4 py-3.5 bg-slate-50 transition" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-rose-500 text-sm font-bold" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm font-bold text-slate-500">Remember me</span>
            </label>
        </div>

        <button type="submit" class="w-full justify-center py-4 px-4 border border-transparent rounded-2xl shadow-sm text-sm font-extrabold text-white bg-indigo-600 hover:bg-indigo-700 hover:shadow-indigo-600/30 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition tracking-wider uppercase mt-2">
            Sign In Now
        </button>
    </form>
</x-guest-layout>
