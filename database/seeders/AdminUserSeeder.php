<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@motorzad.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
            ]
        );

        $admin->assignRole('admin');
    }
}
