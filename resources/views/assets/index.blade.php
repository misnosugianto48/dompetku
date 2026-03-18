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
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6" x-data="{ 
        open: false,
        quantity: '',
        purchasePrice: '',
        currentPrice: '',
        type: 'stock',
        platform: ''
    }">
        <div class="flex justify-between items-center">
            <h3 class="font-semibold text-slate-800">Your Assets</h3>
            <button @click="open = !open" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition shadow-sm">
                <svg class="w-4 h-4" :class="open ? 'rotate-45' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor transition-transform"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                <span x-text="open ? 'Cancel' : 'Add Asset'">Add Asset</span>
            </button>
        </div>
        
        <form method="POST" action="{{ route('assets.store') }}" x-show="open" x-transition class="mt-6 space-y-4 border-t border-slate-50 pt-6">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Asset Name</label>
                    <input type="text" name="name" required placeholder="e.g. BBCA, Gold, SWR013" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Asset Type</label>
                    <select name="type" x-model="type" @change="platform = (type === 'mutual_fund' ? 'Bibit' : (type === 'stock' || type === 'gold' ? 'Nanovest' : ''))" required class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="stock">Stock</option>
                        <option value="gold">Gold</option>
                        <option value="mutual_fund">Mutual Fund</option>
                        <option value="crypto">Crypto</option>
                        <option value="bond">Bond</option>
                    </select>
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Platform</label>
                    <input type="text" name="platform" x-model="platform" placeholder="e.g. Bibit, Nanovest" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <x-form.number-input label="Quantity" name="quantity" model="quantity" required placeholder="0.0000" class="space-y-1.5" />

                <x-form.money-input label="Buy Price/Unit" name="purchase_price" model="purchasePrice" required placeholder="0.00" class="space-y-1.5" />

                <x-form.money-input label="Current Price/Unit" name="current_price" model="currentPrice" required placeholder="0.00" class="space-y-1.5" />
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="px-6 py-2.5 bg-slate-900 text-white text-sm font-semibold rounded-xl hover:bg-slate-800 transition shadow-sm">Save Asset</button>
            </div>
        </form>
    </div>

    <!-- Asset Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($assets as $asset)
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition group">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="inline-block px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-lg bg-indigo-50 text-indigo-700">{{ str_replace('_', ' ', $asset->type) }}</span>
                        @if($asset->platform)
                        <span class="inline-block px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-lg bg-slate-100 text-slate-600">{{ $asset->platform }}</span>
                        @endif
                    </div>
                    <h4 class="font-bold text-slate-900 text-lg">{{ $asset->name }}</h4>
                </div>
                <form action="{{ route('assets.destroy', $asset) }}" method="POST" onsubmit="return confirm('Delete this asset?')">
                    @csrf @method('DELETE')
                    <button class="p-2 text-slate-300 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
                </form>
            </div>
            
            <div class="grid grid-cols-2 gap-y-3 gap-x-4 text-sm mb-4">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Quantity</p>
                    <p class="font-semibold text-slate-700">{{ number_format($asset->quantity, 4) }}</p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Avg. Buy Price</p>
                    <p class="font-medium text-slate-700">Rp {{ number_format($asset->purchase_price, 2, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Current Price</p>
                    <p class="font-medium text-slate-700">Rp {{ number_format($asset->current_price, 2, ',', '.') }}</p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Market Value</p>
                    <p class="font-bold text-indigo-600">Rp {{ number_format($asset->total_value, 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="flex items-center justify-between border-t border-slate-50 pt-4">
                <div class="flex flex-col">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Unrealized P/L</p>
                    <div class="flex items-baseline gap-1">
                        <span class="text-sm font-bold {{ $asset->gain_loss >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $asset->gain_loss >= 0 ? '+' : '' }}Rp {{ number_format($asset->gain_loss, 0, ',', '.') }}
                        </span>
                        <span class="text-[10px] font-bold {{ $asset->gain_loss >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                            ({{ $asset->gain_loss >= 0 ? '+' : '' }}{{ number_format($asset->gain_loss_percent, 2) }}%)
                        </span>
                    </div>
                </div>

                <!-- Actions -->
                <div x-data="{ 
                    editing: false,
                    newPrice: '{{ $asset->current_price }}',
                }" x-init="newPrice = DompetkuNumberFormat.formatNumber(newPrice)" class="flex items-center gap-2">
                    <a href="{{ route('transactions.create', ['asset_id' => $asset->id]) }}" class="text-[11px] font-bold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider bg-emerald-50 px-3 py-1.5 rounded-lg transition">Buy More</a>
                    
                    <button type="button" @click="editing = true" x-show="!editing" class="text-[11px] font-bold text-indigo-600 hover:text-indigo-700 uppercase tracking-wider bg-indigo-50 px-3 py-1.5 rounded-lg transition">Update</button>
                    
                    <form method="POST" action="{{ route('assets.update', $asset) }}" x-show="editing" @click.away="editing = false" class="flex items-center gap-2" x-transition>
                        @csrf @method('PUT')
                        <input type="hidden" name="quantity" value="{{ $asset->quantity }}">
                        <div class="relative">
                            <input type="text" x-model="newPrice" @input="newPrice = DompetkuNumberFormat.formatNumber($event.target.value)" class="w-32 px-2 py-1 text-xs rounded-lg border-slate-200 focus:ring-indigo-500 focus:border-indigo-500">
                            <input type="hidden" name="current_price" :value="DompetkuNumberFormat.getRaw(newPrice)">
                        </div>
                        <button type="submit" class="p-1.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg></button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
