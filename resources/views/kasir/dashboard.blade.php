<x-layouts.app>

<x-slot:title>
    Dashboard Kasir
</x-slot:title>

<div class="p-6 bg-white shadow-md rounded-lg">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Halo, {{ Auth::user()->name }}!</h1>
        <p class="text-gray-600">Selamat bekerja. Anda memiliki akses ke transaksi dan data member.</p>
    </div>

    <!-- Statistik Utama -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Total Produk -->
        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-700">Produk Tersedia</h3>
            <p class="text-2xl font-bold text-black">{{ $totalProducts }}</p>
        </div>

        <!-- Total Member -->
        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-700">Jumlah Member</h3>
            <p class="text-2xl font-bold text-black">{{ $totalMembers }}</p>
        </div>
    </div>

    <!-- Statistik Harian -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Transaksi Hari Ini -->
        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-700">Transaksi Hari Ini</h3>
            <p class="text-2xl font-bold text-black">{{ $totalTransactionsToday }}</p>
        </div>

        <!-- Nilai Transaksi Hari Ini -->
        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-700">Nilai Transaksi Hari Ini</h3>
            <p class="text-2xl font-bold text-black">Rp{{ number_format($totalRevenueToday, 0, ',', '.') }}</p>
        </div>
    </div>
</div>

</x-layouts.app>