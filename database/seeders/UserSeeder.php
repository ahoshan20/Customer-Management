<?php

namespace Database\Seeders;

use App\Enums\ActiveInactive;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@dev.com',
            'password' => Hash::make('password'),
            'status' => ActiveInactive::ACTIVE->value,
        ]);

        User::factory()->count(10)->create();
    }
}
