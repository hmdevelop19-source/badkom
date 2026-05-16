<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'username' => 'admin',
            'fullname' => 'Administrator',
            'email' => 'admin@ebadkom.com',
            'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
            'level' => 'admin',
        ]);
    }
}
