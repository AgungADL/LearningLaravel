<?php

namespace App\Livewire;

use App\Models\Member;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class MemberManager extends Component
{
    use WithPagination;

    // --- Properti State ---
    public $memberId;
    public $name = '';
    public $phone = '';
    public $address = '';
    public $is_active = true;
    public $isModalOpen = false; 
    public $search = '';

    // --- Aturan Validasi ---
    protected function rules()
    {
        return [
            'name' => 'required|min:3|max:100',
            'phone' => 'required|numeric|unique:members,phone,' . $this->memberId,
            'address' => 'nullable|max:255',
        ];
    }
    
    // --- Hook Livewire: Reset Validasi saat mencari ---
    public function updatingSearch()
    {
        $this->resetPage();
    }

    // --- Aksi CRUD ---

    public function create() // Boleh diakses oleh Admin & Kasir
    {
        $this->resetInputFields();
        $this->resetValidation();
        $this->isModalOpen = true;
    }

    public function edit($id) // HANYA boleh diakses oleh Admin
    {
        if (Auth::user()->role !== 'admin') {
            session()->flash('error', 'Akses Ditolak: Hanya Admin yang dapat mengedit data member.');
            return;
        }

        $member = Member::findOrFail($id);
        $this->memberId = $id;
        $this->name = $member->name;
        $this->phone = $member->phone;
        $this->address = $member->address;
        $this->is_active = $member->is_active;

        $this->resetValidation();
        $this->isModalOpen = true;
    }

    public function store() // Menyimpan/Memperbarui Data
    {
        // Jika mode edit, cek apakah user adalah admin
        if ($this->memberId && Auth::user()->role !== 'admin') {
            session()->flash('error', 'Akses Ditolak: Hanya Admin yang dapat memperbarui data.');
            return;
        }
        
        $validatedData = $this->validate();

        // Admin boleh mengubah status aktif/blokir, Kasir tidak.
        $data = [
            'name' => $validatedData['name'],
            'phone' => $validatedData['phone'],
            'address' => $validatedData['address'] ?? null,
        ];
        
        // Hanya Admin yang bisa mengatur status is_active (blokir)
        if (Auth::user()->role === 'admin') {
            $data['is_active'] = $this->is_active;
        }

        Member::updateOrCreate(['id' => $this->memberId], $data);

        session()->flash('message', 
            $this->memberId ? 'Data Member berhasil diperbarui.' : 'Member baru berhasil didaftarkan.');
        
        $this->closeModal();
        $this->resetInputFields();
    }

    public function blockToggle($id) // Blokir/Aktifkan (Hanya Admin)
    {
        if (Auth::user()->role !== 'admin') {
            session()->flash('error', 'Akses Ditolak: Hanya Admin yang dapat memblokir member.');
            return;
        }
        
        $member = Member::findOrFail($id);
        $member->is_active = !$member->is_active; // Toggle status
        $member->save();
        
        $status = $member->is_active ? 'diaktifkan' : 'diblokir';
        session()->flash('message', "Member {$member->name} berhasil {$status}.");
    }

    public function delete($id) // Hapus Permanen (Hanya Admin)
    {
        if (Auth::user()->role !== 'admin') {
            session()->flash('error', 'Akses Ditolak: Hanya Admin yang dapat menghapus data member.');
            return;
        }
        
        Member::find($id)->delete();
        session()->flash('message', 'Member berhasil dihapus secara permanen.');
    }
    
    // --- Helpers ---

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetValidation();
    }

    public function resetInputFields()
    {
        $this->memberId = null;
        $this->name = '';
        $this->phone = '';
        $this->address = '';
        $this->is_active = true; // Default saat tambah baru
    }

    public function render()
    {
        $members = Member::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('phone', 'like', '%' . $this->search . '%')
            ->orderBy('id', 'desc')
            ->paginate(10);
        
        return view('livewire.member-manager', [
            'members' => $members,
        ]);
    }
}
