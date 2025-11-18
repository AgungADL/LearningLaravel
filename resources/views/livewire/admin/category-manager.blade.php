<x-slot:title>
    Kelola Kategori
</x-slot:title>

<div class="p-6 bg-white shadow-lg rounded-lg">
    @if (session()->has('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded" role="alert">
            {{ session('message') }}
        </div>
    @endif

    <div class="flex justify-between mb-4">
        <input 
            wire:model.live.debounce.300ms="search" 
            type="text" 
            placeholder="Cari Nama Kategori..." 
            class="p-2 border border-gray-300 rounded-lg w-1/3 focus:ring-1 focus:ring-black focus:border-black transition"
        >
        <button 
            wire:click="create()" 
            class="bg-black text-white py-2 px-4 rounded-lg hover:bg-gray-900 transition duration-150 font-semibold"
        >
            + Tambah Kategori Baru
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 rounded-lg">
            <thead class="bg-gray-100">
                <tr>
                    <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">#</th>
                    <th class="py-3 px-4 border-b text-left text-sm font-semibold text-gray-700">Nama Kategori</th>
                    <th class="py-3 px-4 border-b text-center text-sm font-semibold text-gray-700">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $index => $category)
                    <tr wire:key="{{ $category->id }}" class="hover:bg-gray-50">
                        <td class="py-3 px-4 border-b text-gray-700">{{ $categories->firstItem() + $index }}</td>
                        <td class="py-3 px-4 border-b text-gray-800">{{ $category->name }}</td>
                        <td class="py-3 px-4 border-b text-center">
                            <div class="flex justify-center space-x-2">
                                <button 
                                    wire:click="edit({{ $category->id }})" 
                                    class="text-gray-700 hover:text-black"
                                    title="Edit"
                                >
                                    <x-heroicon-o-pencil class="w-4 h-4 mr-1" />
                                </button>
                                <button 
                                    wire:click="delete({{ $category->id }})" 
                                    onclick="confirm('Yakin ingin menghapus kategori {{ $category->name }}?') || event.stopImmediatePropagation()" 
                                    class="text-gray-500 hover:text-red-600"
                                    title="Hapus"
                                >
                                    <x-heroicon-o-trash class="w-4 h-4 mr-1" />
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="py-8 text-center text-gray-500">Data kategori kosong.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $categories->links() }}
    </div>

    @if($isModalOpen)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 overflow-y-auto h-full w-full flex items-center justify-center p-4">
        <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-md border border-gray-200">
            <h3 class="text-xl font-bold mb-4 border-b pb-2 text-gray-800">
                {{ $categoryId ? 'Edit Kategori' : 'Tambah Kategori Baru' }}
            </h3>
            
            <form wire:submit.prevent="store">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori</label>
                    <input 
                        wire:model="name" 
                        type="text" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:border-black transition"
                    >
                    @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
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