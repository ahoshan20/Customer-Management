<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            $user->addresses()->create([
                'label' => 'Home',
                'line_1' => '123 Main Street',
                'line_2' => 'Apt 4B',
                'city' => 'Dhaka',
                'state' => 'Dhaka',
                'postal_code' => '1200',
                'country' => 'BD',
                'is_primary' => true,
            ]);

            $user->addresses()->create([
                'label' => 'Office',
                'line_1' => 'Business Road 45',
                'line_2' => null,
                'city' => 'Dhaka',
                'state' => 'Dhaka',
                'postal_code' => '1212',
                'country' => 'BD',
                'is_primary' => false,
            ]);
        }
    }
}
