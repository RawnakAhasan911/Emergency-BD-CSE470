<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash; // <-- Make sure this is here

class AdminSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'admin@emergency.bd'], 
            [
                'name' => 'System Admin',
                'password' => 'admin1234', // <-- Put Hash::make back!
                'role' => 'admin', 
                'status' => 'active'
            ]
        );
    }
}