<x-slot:title>
    Laporan Penjualan
</x-slot:title>

<div class="p-6 bg-white shadow-lg rounded-lg">
    <div class="p-4 border border-gray-200 rounded-lg mb-6">
        <h3 class="font-semibold text-lg mb-3 text-gray-800">Filter Laporan</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Awal</label>
                <input 
                    type="date" 
                    wire:model.live="startDate" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:border-black transition"
                >
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                <input 
                    type="date" 
                    wire:model.live="endDate" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:border-black transition"
                >
            </div>
            @if (Auth::user()->role === 'admin')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Filter Kasir</label>
                <select 
                    wire:model.live="kasirId" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:border-black transition"
                >
                    <option value="">Semua Kasir</option>
                    @foreach ($allKasirs as $kasir)
                        <option value="{{ $kasir->id }}">{{ $kasir->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mode Tampilan</label>
                <select 
                    wire:model.live="viewMode" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:border-black transition"
                >
                    <option value="summary">Ringkasan</option>
                    <option value="detail">Detail</option>
                </select>
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

    @if ($viewMode === 'summary')
        <!-- Tabel Ringkasan -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">#</th>
                        <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">ID Transaksi</th>
                        <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">Tanggal</th>
                        <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">Kasir</th>
                        <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">Member</th>
                        <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">Total Akhir</th>
                        <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">Metode Pembayaran</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $index => $transaction)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4 border-b text-gray-700">{{ $transactions->firstItem() + $index }}</td>
                            <td class="py-3 px-4 border-b text-gray-800">#{{ $transaction->id }}</td>
                            <td class="py-3 px-4 border-b text-gray-800">{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-3 px-4 border-b text-gray-800">{{ $transaction->user->name }}</td>
                            <td class="py-3 px-4 border-b text-gray-800">{{ $transaction->member->name ?? 'Non-Member' }}</td>
                            <td class="py-3 px-4 border-b text-gray-800">Rp{{ number_format($transaction->grand_total, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 border-b text-gray-800">{{ $transaction->payment_method }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500">Tidak ada data transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $transactions->links() }}
        </div>
    @else
        <!-- Tabel Detail -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">#</th>
                        <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">ID Transaksi</th>
                        <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">Tanggal</th>
                        <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">Kasir</th>
                        <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">Member</th>
                        <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">Nama Produk</th>
                        <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">Qty</th>
                        <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">Harga Satuan</th>
                        <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($details as $index => $detail)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4 border-b text-gray-700">{{ $details->firstItem() + $index }}</td>
                            <td class="py-3 px-4 border-b text-gray-800">#{{ $detail->transaction->id }}</td>
                            <td class="py-3 px-4 border-b text-gray-800">{{ $detail->transaction->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-3 px-4 border-b text-gray-800">{{ $detail->transaction->user->name }}</td>
                            <td class="py-3 px-4 border-b text-gray-800">{{ $detail->transaction->member->name ?? 'Non-Member' }}</td>
                            <td class="py-3 px-4 border-b text-gray-800">{{ $detail->product_name }}</td>
                            <td class="py-3 px-4 border-b text-gray-800">{{ $detail->quantity }}</td>
                            <td class="py-3 px-4 border-b text-gray-800">Rp{{ number_format($detail->price, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 border-b text-gray-800">Rp{{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-gray-500">Tidak ada data detail transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $details->links() }}
        </div>
    @endif
</div>