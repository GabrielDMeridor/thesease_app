<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Insert Admin User
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@admin.com'], // Condition to check
            [
                'name' => 'Admin User',
                'password' => hash('sha256', '123456'), // Use SHA-256 to hash the password
                'account_type' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );  
    }
}

