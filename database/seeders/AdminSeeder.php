<?php

namespace Database\Seeders;

use App\Enums\ActiveInactive;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::where('email', 'superadmin@dev.com')->first();

        if ($adminUser) {
            Admin::create([
                'user_id' => $adminUser->id,
                'status' => ActiveInactive::ACTIVE->value,
            ]);
        }
    }
}
