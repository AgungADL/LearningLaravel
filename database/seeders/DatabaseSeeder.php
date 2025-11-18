<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'name' => 'Admin KasirQ',
            'email' => 'admin@kasirq.test',
            'password' => Hash::make('passwordadmin'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Abdul Malik',
            'email' => 'kasir@kasirq.test',
            'password' => Hash::make('passwordkasir'),
            'role' => 'kasir',
        ]);

        $settings = [
            ['key' => 'discount_min_spend', 'value' => '100000'],
            ['key' => 'discount_min_spend_percent', 'value' => '5'],
            ['key' => 'member_discount_percent', 'value' => '10'],
            ['key' => 'store_name', 'value' => 'KasirQ'],
        ];
        Setting::insert($settings);
    }
}
