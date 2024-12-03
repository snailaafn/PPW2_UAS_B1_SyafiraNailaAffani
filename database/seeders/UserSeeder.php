<?php

namespace Database\Seeders;

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
        User::updateOrCreate(
        ['email' => 'admin@mail.com'],  // Kondisi pencarian berdasarkan email
                [
                            'name' => 'Super Admin',
                            'password' => Hash::make('admin'),  // Pastikan password aman
            ]
        );
    }
}
