<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ProductManager extends Component
{
    use WithPagination, WithFileUploads;

    // Properti State
    public $productId;
    public $name = '';
    public $price = 0;
    public $cost = 0;
    public $stock = 0;
    public $category_id = ''; 
    public $image;
    public $oldImage;
    public $isModalOpen = false;
    public $search = '';

    // Aksi CRUD
    public function create()
    {
        $this->resetInputFields();
        $this->resetValidation();
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $this->productId = $id;
        $this->name = $product->name;
        $this->price = $product->price;
        $this->cost = $product->cost;
        $this->stock = $product->stock;
        $this->category_id = $product->category_id;
        $this->oldImage = $product->image;
        $this->image = null;

        $this->resetValidation();
        $this->isModalOpen = true;
    }

    public function store()
    {
        $validatedData = $this->validate([
            'name' => [
                'required',
                'min:3',
                'max:100',
                Rule::unique('products')->where(function ($query) {
                    return $query->where('category_id', $this->category_id);
                })->ignore($this->productId), // Jika update, abaikan produk yang sedang diedit
            ],
            'price' => 'required|integer|min:1',
            'cost' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => $this->productId ? 'nullable|sometimes|image|max:1024' : 'required|image|max:1024',
        ]);

        if ($this->image) {
            if ($this->oldImage) {
                Storage::disk('public')->delete($this->oldImage);
            }

            $validatedData['image'] = $this->image->store('products', 'public');
        } else {
            $validatedData['image'] = $this->oldImage;
        }

        // Simpan produk lama untuk perbandingan
        $oldProduct = null;
        if ($this->productId) {
            $oldProduct = Product::find($this->productId);
        }

        $product = Product::updateOrCreate(['id' => $this->productId], $validatedData);

        // Cek apakah stok berubah
        if ($oldProduct) {
            $stockDiff = $product->stock - $oldProduct->stock;

            if ($stockDiff > 0) {
                // Stok bertambah → pemasukan
                StockMovement::create([
                    'product_id' => $product->id,
                    'quantity' => $stockDiff,
                    'type' => 'in',
                    'source' => 'admin_update',
                ]);
            } elseif ($stockDiff < 0) {
                // Stok berkurang → pengeluaran
                StockMovement::create([
                    'product_id' => $product->id,
                    'quantity' => $stockDiff, // Negatif
                    'type' => 'out',
                    'source' => 'admin_update',
                ]);
            }
        } else {
            // Produk baru → jika stok > 0, catat sebagai pemasukan
            if ($product->stock > 0) {
                StockMovement::create([
                    'product_id' => $product->id,
                    'quantity' => $product->stock,
                    'type' => 'in',
                    'source' => 'admin_create',
                ]);
            }
        }

        session()->flash('message', $this->productId ? 'Produk berhasil diperbarui.' : 'Produk berhasil ditambahkan.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function delete($id)
    {
        $product = Product::find($id);

        // Hapus file gambar jika ada
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        // Catat stok sisa sebagai pengeluaran
        if ($product->stock > 0) {
            StockMovement::create([
                'product_id' => $product->id,
                'quantity' => -$product->stock, // Negatif
                'type' => 'out',
                'source' => 'admin_delete',
            ]);
        }

        $product->delete();
        session()->flash('message', 'Produk berhasil dihapus.');
    }

    // Helpers
    public function closeModal()
    {
        $this->resetInputFields();
        $this->isModalOpen = false;
    }

    public function resetInputFields()
    {
        $this->productId = null;
        $this->name = '';
        $this->price = 0;
        $this->cost = 0;
        $this->stock = 0;
        $this->category_id = '';
        $this->image = null;
        $this->oldImage = null;
    }

    // Render view dan ambil data
    public function render()
    {
        $query = Product::with('category')->where('name', 'like', '%' . $this->search . '%');

        if ($this->category_id) {
            $query->where('category_id', $this->category_id);
        }

        $products = $query->orderBy('id', 'desc')->paginate(10);
        $categories = Category::all();

        return view('livewire.admin.product-manager', [
            'products' => $products,
            'categories' => $categories
        ]);
    }
}
