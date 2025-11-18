<x-slot:title>
    Kelola Inventaris Produk
</x-slot:title>

<div class="p-6 bg-white shadow-lg rounded-lg">
    
    @if (session()->has('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded" role="alert">
            {{ session('message') }}
        </div>
    @endif

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
        <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3 w-full sm:w-2/3">
            <input 
                wire:model.live.debounce.300ms="search" 
                type="text" 
                placeholder="Cari Nama Produk..." 
                class="p-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:border-black transition flex-grow"
            >
            
            <select 
                wire:model.live="category_id" 
                class="p-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:border-black transition w-40"
            >
                <option value="">Semua Kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <button 
            wire:click="create()" 
            class="bg-black text-white py-2 px-4 rounded-lg hover:bg-gray-900 transition duration-150 font-semibold whitespace-nowrap"
        >
            + Tambah Produk Baru
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 rounded-lg">
            <thead class="bg-gray-100">
                <tr>
                    <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">#</th>
                    <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">Foto</th>
                    <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">Nama Produk</th>
                    <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">Kategori</th>
                    <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">Harga Jual</th>
                    <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">Cost (HPP)</th>
                    <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">Stok</th>
                    <th class="py-3 px-4 border-b text-center text-sm font-semibold text-gray-700">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $index => $product)
                    <tr wire:key="{{ $product->id }}" class="hover:bg-gray-50">
                        <td class="py-3 px-4 border-b text-gray-700">{{ $products->firstItem() + $index }}</td>
                        <td class="py-3 px-4 border-b">
                            @if ($product->image)
                                <img 
                                    src="{{ asset('storage/' . $product->image) }}" 
                                    alt="{{ 'Foto ' . $product->name  }}" 
                                    class="h-16 w-16 object-cover mx-auto rounded-md border border-gray-200"
                                >
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 border-b text-gray-800">{{ $product->name }}</td>
                        <td class="py-3 px-4 border-b text-gray-800">{{ $product->category->name ?? '-' }}</td>
                        <td class="py-3 px-4 border-b text-gray-800">Rp{{ number_format($product->price, 0, ',', '.') }}</td>
                        <td class="py-3 px-4 border-b text-gray-800">Rp{{ number_format($product->cost, 0, ',', '.') }}</td>
                        <td class="py-3 px-4 border-b text-gray-800">{{ $product->stock }}</td>
                        <td class="py-3 px-4 border-b text-center">
                            <div class="flex justify-center space-x-2">
                                <button 
                                    wire:click="edit({{ $product->id }})" 
                                    class="text-gray-700 hover:text-black"
                                    title="Edit"
                                >
                                    <x-heroicon-o-pencil class="w-4 h-4" />
                                </button>
                                <button 
                                    wire:click="delete({{ $product->id }})" 
                                    onclick="confirm('Yakin ingin menghapus {{ $product->name }}?') || event.stopImmediatePropagation()" 
                                    class="text-gray-500 hover:text-red-600"
                                    title="Hapus"
                                >
                                    <x-heroicon-o-trash class="w-4 h-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-gray-500">Data produk kosong. Silakan tambah produk baru!</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $products->links() }}
    </div>

    @if($isModalOpen)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 overflow-y-auto h-full w-full flex items-center justify-center p-4">
        <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-md border border-gray-200">
            <h3 class="text-xl font-bold mb-4 border-b pb-2 text-gray-800">
                {{ $productId ? 'Edit Produk' : 'Tambah Produk Baru' }}
            </h3>
            
            <form wire:submit.prevent="store">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk</label>
                    <input 
                        wire:model="name" 
                        type="text" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:border-black transition"
                    >
                    @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select 
                        wire:model="category_id" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:border-black transition"
                    >
                        <option value="">Pilih Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                </div>
                
                <div class="grid grid-cols-3 gap-3 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Harga Jual</label>
                        <input 
                            wire:model="price" 
                            type="number" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:border-black transition"
                        >
                        @error('price') <span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cost (HPP)</label>
                        <input 
                            wire:model="cost" 
                            type="number" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:border-black transition"
                        >
                        @error('cost') <span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Stok</label>
                        <input 
                            wire:model="stock" 
                            type="number" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:border-black transition"
                        >
                        @error('stock') <span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Foto Produk</label>
                    <input 
                        wire:model="image" 
                        type="file" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:border-black transition"
                    >
                    @error('image') <span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror

                    @if ($image)
                        <p class="text-sm mt-2 text-gray-600">Preview Foto Baru:</p>
                        <img src="{{ $image->temporaryUrl() }}" class="h-20 w-20 object-cover border rounded-lg mt-1 border-gray-200">
                    @elseif ($oldImage)
                        <p class="text-sm mt-2 text-gray-600">Foto Saat Ini:</p>
                        <img src="{{ asset('storage/' . $oldImage) }}" class="h-20 w-20 object-cover border rounded-lg mt-1 border-gray-200">
                    @endif
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button 
                        type="button" 
                        wire:click="closeModal()" 
                        class="bg-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-400 transition duration-150"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit" 
                        class="bg-black text-white py-2 px-4 rounded-lg hover:bg-gray-900 transition duration-150"
                    >
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>