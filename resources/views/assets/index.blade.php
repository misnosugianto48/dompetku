@extends('layouts.app')
@section('title', 'Digital Assets')

@section('content')
<div class="space-y-6">
    <!-- Summary -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
            <p class="text-xs font-medium text-slate-500">Total Invested</p>
            <h3 class="text-xl font-bold text-slate-900 mt-1">Rp {{ number_format($totalInvested, 0, ',', '.') }}</h3>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
            <p class="text-xs font-medium text-slate-500">Current Value</p>
            <h3 class="text-xl font-bold text-indigo-600 mt-1">Rp {{ number_format($totalPortfolioValue, 0, ',', '.') }}</h3>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
            <p class="text-xs font-medium text-slate-500">Gain/Loss</p>
            @php $gl = $totalPortfolioValue - $totalInvested; @endphp
            <h3 class="text-xl font-bold mt-1 {{ $gl >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                {{ $gl >= 0 ? '+' : '' }}Rp {{ number_format($gl, 0, ',', '.') }}
            </h3>
        </div>
    </div>

    <!-- Add Asset Form -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6" x-data="{ open: false }">
        <div class="flex justify-between items-center">
            <h3 class="font-semibold text-slate-800">Your Assets</h3>
            <button @click="open = !open" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                <span class="hidden sm:inline">Add Asset</span>
            </button>
        </div>
        <form method="POST" action="{{ route('assets.store') }}" x-show="open" x-transition class="mt-4 grid grid-cols-1 sm:grid-cols-5 gap-3">
            @csrf
            <input type="text" name="name" required placeholder="Asset name (e.g. BBCA)" class="rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
            <select name="type" required class="rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="stock">Stock</option>
                <option value="gold">Gold</option>
                <option value="mutual_fund">Mutual Fund</option>
                <option value="crypto">Crypto</option>
                <option value="bond">Bond</option>
            </select>
            <input type="number" name="quantity" step="0.0001" min="0" required placeholder="Quantity" class="rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
            <input type="number" name="purchase_price" step="1" min="0" required placeholder="Buy price/unit" class="rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
            <input type="number" name="current_price" step="1" min="0" required placeholder="Current price/unit" class="rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
            <button type="submit" class="sm:col-span-5 px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-xl hover:bg-slate-900 transition">Save Asset</button>
        </form>
    </div>

    <!-- Asset Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($assets as $asset)
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-lg bg-indigo-50 text-indigo-700 mb-1">{{ str_replace('_', ' ', ucfirst($asset->type)) }}</span>
                    <h4 class="font-semibold text-slate-800">{{ $asset->name }}</h4>
                </div>
                <form action="{{ route('assets.destroy', $asset) }}" method="POST" onsubmit="return confirm('Delete this asset?')">
                    @csrf @method('DELETE')
                    <button class="text-slate-300 hover:text-rose-500 transition"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
                </form>
            </div>
            <div class="space-y-1 text-sm">
                <div class="flex justify-between"><span class="text-slate-500">Qty</span><span class="font-medium">{{ number_format($asset->quantity, 4) }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Buy Price</span><span>Rp {{ number_format($asset->purchase_price, 0, ',', '.') }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Current</span><span>Rp {{ number_format($asset->current_price, 0, ',', '.') }}</span></div>
                <div class="flex justify-between border-t border-slate-100 pt-1 mt-1">
                    <span class="text-slate-500">Value</span>
                    <span class="font-bold">Rp {{ number_format($asset->total_value, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">P/L</span>
                    <span class="font-semibold {{ $asset->gain_loss >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ $asset->gain_loss >= 0 ? '+' : '' }}{{ number_format($asset->gain_loss_percent, 2) }}%
                    </span>
                </div>
            </div>

            <!-- Update Price -->
            <form method="POST" action="{{ route('assets.update', $asset) }}" class="mt-3 pt-3 border-t border-slate-100" x-data="{ editing: false }">
                @csrf @method('PUT')
                <button type="button" @click="editing = !editing" x-show="!editing" class="text-xs text-indigo-600 hover:text-indigo-700 font-medium">Update Price</button>
                <div x-show="editing" x-transition class="flex gap-2">
                    <input type="hidden" name="quantity" value="{{ $asset->quantity }}">
                    <input type="number" name="current_price" step="1" value="{{ $asset->current_price }}" class="flex-1 rounded-lg border-slate-200 text-xs focus:ring-indigo-500 focus:border-indigo-500">
                    <button type="submit" class="px-3 py-1 bg-indigo-600 text-white text-xs rounded-lg hover:bg-indigo-700 transition">Save</button>
                </div>
            </form>
        </div>
        @endforeach
    </div>
</div>
@endsection
