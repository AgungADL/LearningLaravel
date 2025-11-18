<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryManager extends Component
{
    use WithPagination;

    public $categoryId;
    public $name = '';
    public $isModalOpen = false;
    public $search = '';

    protected function rules()
    {
        return [
            'name' => 'required|min:3|unique:categories,name,' . $this->categoryId,
        ];
    }

    public function create()
    {
        $this->resetInputFields();
        $this->resetValidation();
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $this->categoryId = $id;
        $this->name = $category->name;

        $this->resetValidation();
        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate();

        Category::updateOrCreate(['id' => $this->categoryId], ['name' => $this->name]);

        session()->flash('message', $this->categoryId ? 'Kategori berhasil diperbarui.' : 'Kategori berhasil ditambahkan.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function delete($id)
    {
        Category::find($id)->delete();
        session()->flash('message', 'Kategori berhasil dihapus.');
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetValidation();
    }

    public function resetInputFields()
    {
        $this->categoryId = null;
        $this->name = '';
    }

    public function render()
    {
        $categories = Category::where('name', 'like', '%' . $this->search . '%')
            ->orderBy('id', 'desc')
            ->paginate(10);
        
        return view('livewire.admin.category-manager', [
            'categories' => $categories,
        ]);
    }
}
