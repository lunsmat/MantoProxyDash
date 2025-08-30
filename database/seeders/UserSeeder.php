<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()
            ->count(9)
            ->create();

        User::factory()->create([
            'email' => 'admin@example.com',
            'name' => 'Admin User',
            'password' => Hash::make('admin'),
            'role' => UserRole::Admin->getLabel(),
        ]);
    }
}
