<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class UserManager extends Component
{
    use WithPagination;

    public $userId;
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $isModalOpen = false;
    public $search = '';

    protected function rules()
    {
        return [
            'name' => 'required|min:3|max:100',
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'password' => $this->userId ? 'nullable|min:6|confirmed' : 'required|min:6|confirmed',
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
        $user = User::findOrFail($id);
        $this->userId = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->password_confirmation = '';

        $this->resetValidation();
        $this->isModalOpen = true;
    }

    public function store()
    {
        // Validasi berjalan di sini. Jika Create, password WAJIB ADA. Jika Edit, password OPTIONAL.
        $validatedData = $this->validate();

        // Siapkan data dasar
        $data = [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'role' => 'kasir',
        ];

        // CUKUP cek apakah password diisi. Jika diisi, maka hash dan masukkan ke $data.
        // Jika tidak diisi (saat Edit), data password lama tidak disentuh oleh updateOrCreate.
        if (!empty($validatedData['password'])) {
            $data['password'] = Hash::make($validatedData['password']);
        }

        User::updateOrCreate(['id' => $this->userId], $data);

        session()->flash(
            'message',
            $this->userId ? 'Akun Kasir berhasil diperbarui.' : 'Akun Kasir baru berhasil ditambahkan.'
        );

        $this->closeModal();
        $this->resetInputFields();
    }

    public function delete($id) // Menghapus Data
    {
        if ($id === Auth::id()) { // <<< PERUBAHAN DI SINI
            session()->flash('error', 'Anda tidak bisa menghapus akun yang sedang login.');
            return;
        }

        User::find($id)->delete();
        session()->flash('message', 'Akun Kasir berhasil dihapus.');
    }

    // --- Helpers ---

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetValidation();
    }

    public function resetInputFields()
    {
        $this->userId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
    }

    public function render()
    {
        // Ambil data user yang rolenya 'kasir'
        $users = User::where('role', 'kasir')
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.admin.user-manager', [
            'users' => $users,
        ]);
    }
}
