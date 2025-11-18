<x-slot:title>
    Pengaturan Diskon Global
</x-slot:title>

<div class="p-6 bg-white shadow-lg rounded-lg">
    @if (session()->has('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded" role="alert">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit.prevent="store" class="space-y-6">
        
        <!-- Diskon Tetap Member -->
        <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
            <h3 class="font-semibold text-lg mb-3 text-gray-800">1. Diskon Tetap untuk Member</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="member_discount_percent" class="block text-sm font-medium text-gray-700 mb-1">Persentase Diskon Member (%)</label>
                    <input 
                        wire:model="member_discount_percent" 
                        type="number" 
                        id="member_discount_percent" 
                        min="0" 
                        max="100" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:border-black transition"
                    >
                    @error('member_discount_percent') <span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                    <p class="text-xs text-gray-500 mt-1">Diskon yang selalu diberikan kepada member (contoh: 10).</p>
                </div>
            </div>
        </div>

        <!-- Diskon Minimal Belanja (Khusus Member) -->
        <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
            <h3 class="font-semibold text-lg mb-3 text-gray-800">2. Diskon Minimal Belanja (Khusus Member)</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="discount_min_spend" class="block text-sm font-medium text-gray-700 mb-1">Minimal Belanja untuk Diskon (Rp)</label>
                    <input 
                        wire:model="discount_min_spend" 
                        type="number" 
                        id="discount_min_spend" 
                        min="0" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:border-black transition"
                    >
                    @error('discount_min_spend') <span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                    <p class="text-xs text-gray-500 mt-1">Contoh: 100000. Jika subtotal mencapai nilai ini.</p>
                </div>
                <div>
                    <label for="discount_min_spend_amount" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Diskon (Rp)</label>
                    <input 
                        wire:model="discount_min_spend_amount" 
                        type="number" 
                        id="discount_min_spend_amount" 
                        min="0" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:border-black transition"
                    >
                    @error('discount_min_spend_amount') <span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                    <p class="text-xs text-gray-500 mt-1">Jumlah diskon tetap yang diberikan (contoh: 5000).</p>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button 
                type="submit" 
                class="bg-black text-white py-2 px-6 rounded-lg font-semibold hover:bg-gray-900 transition duration-150"
            >
                Simpan Pengaturan
            </button>
        </div>
    </form>
</div>