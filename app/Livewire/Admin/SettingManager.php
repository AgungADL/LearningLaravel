<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use Livewire\Component;

class SettingManager extends Component
{
    public $member_discount_percent;
    public $discount_min_spend;
    public $discount_min_spend_amount;

    public function mount()
    {
        $settings = Setting::getSettings();

        $this->member_discount_percent = (int)($settings['member_discount_percent'] ?? 0);
        $this->discount_min_spend = (int)($settings['discount_min_spend'] ?? 0);
        $this->discount_min_spend_amount = (int)($settings['discount_min_spend_amount'] ?? 0);
    }

    protected $rules = [
        'member_discount_percent' => 'required|integer|min:0|max:100',
        'discount_min_spend' => 'required|integer|min:0',
        'discount_min_spend_amount' => 'required|integer|min:0',
    ];

    public function store()
    {
        $this->validate();

        Setting::setSetting('member_discount_percent', $this->member_discount_percent);
        Setting::setSetting('discount_min_spend', $this->discount_min_spend);
        Setting::setSetting('discount_min_spend_amount', $this->discount_min_spend_amount);

        session()->flash('message', 'Pengaturan diskon berhasil diperbarui.');
    }

    public function render()
    {
        return view('livewire.admin.setting-manager');
    }
}
