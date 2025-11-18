<x-slot:title>
    Laporan Pergerakan Stok Mingguan
</x-slot:title>

<div class="p-6 bg-white shadow-lg rounded-lg">

    <div class="p-4 border border-gray-200 rounded-lg mb-6">
        <h3 class="font-semibold text-lg mb-3 text-gray-800">Filter Laporan</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai Minggu</label>
                <input 
                    type="date" 
                    wire:model.live="weekStartDate" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:border-black transition"
                >
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir Minggu</label>
                <input 
                    type="date" 
                    wire:model="weekEndDate" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:border-black transition"
                    disabled
                >
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cari Produk</label>
                <input 
                    type="text" 
                    wire:model.live="search" 
                    placeholder="Cari nama produk..." 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:border-black transition"
                >
            </div>
        </div>
        <div class="mt-4">
            <button 
                wire:click="exportToExcel" 
                class="bg-black text-white py-2 px-4 rounded-lg hover:bg-gray-900 transition"
            >
                Export ke Excel
            </button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 rounded-lg">
            <thead class="bg-gray-100">
                <tr>
                    <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">#</th>
                    <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">Nama Produk</th>
                    <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">Kategori</th>
                    <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">Satuan</th>
                    <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">Stok Awal</th>
                    <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">Total Restock (IN)</th>
                    <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">Total Pengurangan (OUT)</th>
                    <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">Stok Akhir</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $index => $product)
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-4 border-b text-gray-700">{{ $products->firstItem() + $index }}</td>
                        <td class="py-3 px-4 border-b text-gray-800">{{ $product->name }}</td>
                        <td class="py-3 px-4 border-b text-gray-800">{{ $product->category->name ?? '-' }}</td>
                        <td class="py-3 px-4 border-b text-gray-800">Pcs</td>
                        <td class="py-3 px-4 border-b text-gray-800">{{ $product->stock_summary['stock_start'] }}</td>
                        <td class="py-3 px-4 border-b text-green-600 font-medium">{{ $product->stock_summary['total_in'] }}</td>
                        <td class="py-3 px-4 border-b text-red-600 font-medium">{{ $product->stock_summary['total_out'] }}</td>
                        <td class="py-3 px-4 border-b text-gray-800">{{ $product->stock_summary['stock_end'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-gray-500">Tidak ada data produk.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $products->links() }}
    </div>
</div>