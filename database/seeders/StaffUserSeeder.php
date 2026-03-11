<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StaffUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Jethro Jayson Manzanillo as staff
        User::create([
            'first_name' => 'Jethro Jayson',
            'last_name' => 'Manzanillo',
            'middle_name' => null,
            'suffix' => null,
            'email' => 'jethro.manzanillo@konstructo.com',
            'username' => 'jethro.manzanillo',
            'password' => Hash::make('staff123'), // Easy password for testing
            'role' => 'staff',
            'phone_number' => '09991234567',
            'address' => 'Staff Office, Konstructo Building',
            'zip_code' => '1000',
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Staff user created successfully!');
        $this->command->info('Email: jethro.manzanillo@konstructo.com');
        $this->command->info('Password: staff123');
        $this->command->info('Role: staff');
    }
}