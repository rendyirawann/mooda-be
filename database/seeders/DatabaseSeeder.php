<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Dev: akun demo untuk uji login dari mooda-fe.
     * (Produksi memakai DB bersama stakko_pos, tidak perlu seeder ini.)
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'owner@mooda.test'],
            ['name' => 'Owner Demo', 'password' => Hash::make('password')],
        );
    }
}
