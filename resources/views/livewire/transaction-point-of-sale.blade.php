<x-slot:title>
    Transaksi Point Of Sale (POS)
</x-slot:title>

<div class="p-6 bg-white shadow-lg rounded-lg">
    <!-- Alert Messages -->
    @if (session()->has('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Kolom Kiri: Pencarian & Keranjang -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Pencarian Produk -->
            <div class="p-4 border border-gray-200 rounded-lg">
                <h3 class="font-semibold text-lg mb-3 text-gray-800">Pencarian Produk</h3>
                
                <div class="flex flex-col sm:flex-row gap-3 mb-3">
                    <select 
                        wire:model.live="categoryId" 
                        class="p-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:border-black transition w-40"
                    >
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    
                    <input 
                        wire:model.live="productSearch" 
                        type="text" 
                        placeholder="Cari produk berdasarkan nama..." 
                        class="p-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:border-black transition flex-grow"
                    >
                </div>

                <!-- Hasil Pencarian -->
                @if (count($searchResult) > 0)
                    <div class="mt-3 border border-gray-200 rounded-lg max-h-40 overflow-y-auto">
                        @foreach ($searchResult as $product)
                            <div 
                                wire:click="selectProduct({{ $product->id }})" 
                                class="p-3 border-b border-gray-200 hover:bg-gray-50 cursor-pointer flex justify-between items-center"
                            >
                                @if($product->image)
                                    <img 
                                        src="{{ asset('storage/' . $product->image) }}" 
                                        alt="{{ $product->name }}" 
                                        class="w-10 h-10 object-cover rounded-md mr-3 border border-gray-200"
                                    >
                                @else
                                    <div class="w-10 h-10 bg-gray-200 rounded-md mr-3 flex items-center justify-center text-gray-500">
                                        ?
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <p class="font-medium text-gray-800">{{ $product->name }}</p>
                                    <p class="text-sm text-gray-600">Rp{{ number_format($product->price, 0, ',', '.') }} | Stok: {{ $product->stock }}</p>
                                </div>
                                <span class="text-gray-400">→</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Member -->
            <div class="p-4 border border-gray-200 rounded-lg">
                <h3 class="font-semibold text-lg mb-3 text-gray-800">Member</h3>
                
                @if (session()->has('message_member'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-2 mb-3 rounded text-sm">
                        {{ session('message_member') }}
                    </div>
                @endif
                @if (session()->has('error_member'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-2 mb-3 rounded text-sm">
                        {{ session('error_member') }}
                    </div>
                @endif

                <div class="flex flex-col sm:flex-row gap-3">
                    <input 
                        wire:model="memberPhone" 
                        type="text" 
                        placeholder="Masukkan nomor HP member" 
                        class="p-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:border-black transition flex-grow"
                    >
                    <button 
                        wire:click="findMember" 
                        class="bg-gray-800 text-white py-2 px-4 rounded-lg hover:bg-gray-900 transition"
                    >
                        Cari Member
                    </button>
                    @if ($memberData)
                        <button 
                            wire:click="removeMember" 
                            class="bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition"
                        >
                            Hapus
                        </button>
                    @endif
                </div>
                
                @if ($memberData)
                    <div class="mt-3 p-2 bg-gray-50 rounded-lg border border-gray-200">
                        <p class="font-medium text-gray-800">{{ $memberData->name }}</p>
                        <p class="text-sm text-gray-600">{{ $memberData->phone }}</p>
                    </div>
                @endif
            </div>

            <!-- Keranjang -->
            <div class="p-4 border border-gray-200 rounded-lg">
                <h3 class="font-semibold text-lg mb-3 text-gray-800">Keranjang Belanja</h3>
                
                @if (empty($cart))
                    <p class="text-gray-500 text-center py-4">Keranjang kosong. Tambahkan produk terlebih dahulu.</p>
                @else
                    <div class="space-y-2 max-h-60 overflow-y-auto">
                        @foreach ($cart as $id => $item)
                            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                @if($item['image'])
                                    <img 
                                        src="{{ asset('storage/' . $item['image']) }}" 
                                        alt="{{ $item['name'] }}" 
                                        class="w-10 h-10 object-cover rounded-md mr-3 border border-gray-200"
                                    >
                                @else
                                    <div class="w-10 h-10 bg-gray-200 rounded-md mr-3 flex items-center justify-center text-gray-500 text-xs">
                                        ?
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <p class="font-medium text-gray-800">{{ $item['name'] }}</p>
                                    <p class="text-sm text-gray-600">Rp{{ number_format($item['price'], 0, ',', '.') }}</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button 
                                        wire:click="updateCartQuantity({{ $id }}, {{ $item['qty'] - 1 }})" 
                                        class="w-8 h-8 flex items-center justify-center border border-gray-300 rounded text-gray-600 hover:bg-gray-100"
                                    >
                                        -
                                    </button>
                                    <span class="w-10 text-center">{{ $item['qty'] }}</span>
                                    <button 
                                        wire:click="updateCartQuantity({{ $id }}, {{ $item['qty'] + 1 }})" 
                                        class="w-8 h-8 flex items-center justify-center border border-gray-300 rounded text-gray-600 hover:bg-gray-100"
                                    >
                                        +
                                    </button>
                                    <button 
                                        wire:click="removeItem({{ $id }})" 
                                        class="ml-2 text-red-600 hover:text-red-800"
                                    >
                                        <x-heroicon-o-trash class="w-5 h-5" />
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Kolom Kanan: Ringkasan & Pembayaran -->
        <div class="space-y-6">
            <!-- Ringkasan -->
            <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
                <h3 class="font-semibold text-lg mb-3 text-gray-800">Ringkasan</h3>
                
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="font-medium">Rp{{ number_format($total + $discountAmount, 0, ',', '.') }}</span>
                    </div>
                    @if ($discountAmount > 0)
                        <div class="flex justify-between text-green-600">
                            <span>Diskon:</span>
                            <span>-Rp{{ number_format($discountAmount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-lg font-bold">
                        <span>Total:</span>
                        <span>Rp{{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Item:</span>
                        <span>{{ $totalItems }}</span>
                    </div>
                </div>

                <button 
                    wire:click="openPaymentModal" 
                    class="mt-4 w-full bg-black text-white py-3 rounded-lg hover:bg-gray-900 transition font-semibold"
                >
                    Proses Pembayaran
                </button>
            </div>

            <!-- Riwayat Transaksi -->
            <div class="p-4 border border-gray-200 rounded-lg">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-semibold text-lg text-gray-800">Riwayat Transaksi Hari Ini</h3>
                </div>
                
                @if ($dailyTransactions->isEmpty())
                    <p class="text-gray-500 text-sm text-center py-2">Tidak ada transaksi hari ini.</p>
                @else
                    <div class="space-y-2 max-h-60 overflow-y-auto">
                        @foreach ($dailyTransactions as $transaction)
                            <div class="p-2 border border-gray-200 rounded-lg">
                                <div class="flex justify-between">
                                    <span class="font-medium">#{{ $transaction->id }}</span>
                                    <span class="text-sm text-gray-600">{{ $transaction->created_at->format('H:i') }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span>Total:</span>
                                    <span class="font-semibold">Rp{{ number_format($transaction->grand_total, 0, ',', '.') }}</span>
                                </div>
                                @if ($transaction->member)
                                    <div class="text-xs text-gray-500">
                                        Member: {{ $transaction->member->name }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-2 text-center">
                        <a 
                            href="{{ route('kasir.reports.sales') }}" 
                            class="text-sm text-black hover:text-gray-700"
                        >
                            Lihat Semua Riwayat →
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Pembayaran -->
    @if ($isPaymentModalOpen)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 overflow-y-auto h-full w-full flex items-center justify-center p-4">
        <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-md border border-gray-200">
            <h3 class="text-xl font-bold mb-4 border-b pb-2 text-gray-800">Pembayaran</h3>

            @if (session()->has('error_payment'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">
                    {{ session('error_payment') }}
                </div>
            @endif

            <div class="space-y-4">
                <div class="flex justify-between text-lg font-bold">
                    <span>Total:</span>
                    <span>Rp{{ number_format($total, 0, ',', '.') }}</span>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Diterima</label>
                    <input 
                        wire:model="paidAmount" 
                        wire:change="calculateChange"
                        type="number" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:border-black transition"
                    >
                    @error('paidAmount') <span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                </div>

                <div class="flex justify-between text-lg font-bold">
                    <span>Kembalian:</span>
                    <span class="{{ $changeAmount < 0 ? 'text-red-600' : 'text-green-600' }}">Rp{{ number_format($changeAmount, 0, ',', '.') }}</span>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button 
                        type="button" 
                        wire:click="closeReceiptModal" 
                        class="bg-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-400 transition"
                    >
                        Batal
                    </button>
                    <button 
                        wire:click="processTransaction" 
                        class="bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition"
                    >
                        Bayar
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if ($printedTransactionId)
        <div id="receipt-content" class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 overflow-y-auto h-full w-full flex items-center justify-center p-4">
            <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-md border border-gray-200">
                <div class="text-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">KasirQ</h3>
                    <p class="text-sm text-gray-600">Jl. Contoh No. 123</p>
                    <p class="text-sm text-gray-600">Telp: 0812-3456-7890</p>
                </div>
                
                <div class="text-sm space-y-2 border-t border-b border-gray-200 py-3">
                    <p class="text-center"><strong>STRUK PEMBAYARAN</strong></p>
                    <p><strong>ID Transaksi:</strong> #{{ $printedTransactionId }}</p>
                    <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
                    <p><strong>Kasir:</strong> {{ Auth::user()->name }}</p>
                    @if (isset($receiptData['memberName']) && $receiptData['memberName'])
                        <p><strong>Member:</strong> {{ $receiptData['memberName'] }}</p>
                    @endif
                    <div class="mt-3">
                        <strong>Item:</strong>
                        <ul class="mt-1 space-y-1">
                            @if (isset($receiptData['items']))
                                @foreach ($receiptData['items'] as $item)
                                    <li class="flex justify-between">
                                        <span>{{ $item['name'] }} ({{ $item['qty'] }}x)</span>
                                        <span>Rp{{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}</span>
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                    <div class="mt-2 border-t border-gray-200 pt-2">
                        @if (isset($receiptData['total'], $receiptData['discountAmount']))
                            <p class="flex justify-between"><span>Subtotal:</span> <span>Rp{{ number_format($receiptData['total'] + $receiptData['discountAmount'], 0, ',', '.') }}</span></p>
                            @if ($receiptData['discountAmount'] > 0)
                                <p class="flex justify-between text-green-600"><span>Diskon:</span> <span>-Rp{{ number_format($receiptData['discountAmount'], 0, ',', '.') }}</span></p>
                            @endif
                            <p class="flex justify-between font-bold"><span>Total:</span> <span>Rp{{ number_format($receiptData['total'], 0, ',', '.') }}</span></p>
                            <p class="flex justify-between"><span>Dibayar:</span> <span>Rp{{ number_format($receiptData['paidAmount'], 0, ',', '.') }}</span></p>
                            <p class="flex justify-between"><span>Kembalian:</span> <span>Rp{{ number_format($receiptData['changeAmount'], 0, ',', '.') }}</span></p>
                        @endif
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <p class="text-sm text-gray-600 italic">Terima kasih telah berbelanja!</p>
                    <p class="text-xs text-gray-500 mt-2">Barang yang sudah dibeli tidak dapat ditukar/dikembalikan</p>
                </div>

                <div class="flex justify-end space-x-3 mt-6 print:hidden">
                    <button 
                        wire:click="closeReceiptModal" 
                        class="bg-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-400 transition"
                    >
                        Tutup
                    </button>
                    <button 
                        onclick="window.print()"
                        class="bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition"
                    >
                        Cetak Struk
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>