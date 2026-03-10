<?php
// database/seeders/UserSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Clear existing users first (optional - be careful!)
        // User::truncate(); // Uncomment if you want to clear all users first

        // Create Admin
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'middle_name' => null,
            'suffix' => null,
            'email' => 'admin@konstructo.com',
            'username' => 'admin',
            'password' => Hash::make('Admin123!@#'),
            'role' => 'admin',
            'phone_number' => '09123456789',
            'address' => 'Admin Office, Main Street',
            'zip_code' => '1000',
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create Staff
        $staff = [
            [
                'first_name' => 'John',
                'last_name' => 'Santos',
                'email' => 'john.santos@konstructo.com',
                'username' => 'john.santos',
                'phone_number' => '09234567890',
            ],
            [
                'first_name' => 'Maria',
                'last_name' => 'Reyes',
                'email' => 'maria.reyes@konstructo.com',
                'username' => 'maria.reyes',
                'phone_number' => '09345678901',
            ],
            [
                'first_name' => 'Robert',
                'last_name' => 'Lim',
                'email' => 'robert.lim@konstructo.com',
                'username' => 'robert.lim',
                'phone_number' => '09456789012',
            ],
            [
                'first_name' => 'Patricia',
                'last_name' => 'Villanueva',
                'email' => 'patricia.v@konstructo.com',
                'username' => 'patricia.v',
                'phone_number' => '09567890123',
            ],
            [
                'first_name' => 'Michael',
                'last_name' => 'Tan',
                'email' => 'michael.tan@konstructo.com',
                'username' => 'michael.tan',
                'phone_number' => '09678901234',
            ],
            [
                'first_name' => 'Sarah',
                'last_name' => 'Gonzales',
                'email' => 'sarah.gonzales@konstructo.com',
                'username' => 'sarah.gonzales',
                'phone_number' => '09789012345',
            ],
        ];

        foreach ($staff as $staffMember) {
            User::create([
                'first_name' => $staffMember['first_name'],
                'last_name' => $staffMember['last_name'],
                'middle_name' => null,
                'suffix' => null,
                'email' => $staffMember['email'],
                'username' => $staffMember['username'],
                'password' => Hash::make('Staff123!@#'),
                'role' => 'staff', // Make sure this is 'staff' not 'engineer'
                'phone_number' => $staffMember['phone_number'],
                'address' => 'Staff Address',
                'zip_code' => '1000',
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create Applicants
        $applicants = [
            [
                'first_name' => 'Anna',
                'last_name' => 'Lopez',
                'email' => 'anna.lopez@email.com',
                'username' => 'anna.lopez',
                'phone_number' => '09678901234',
            ],
            [
                'first_name' => 'Mark',
                'last_name' => 'Garcia',
                'email' => 'mark.garcia@email.com',
                'username' => 'mark.garcia',
                'phone_number' => '09789012345',
            ],
            [
                'first_name' => 'Juan',
                'last_name' => 'Dela Cruz',
                'email' => 'juan.delacruz@email.com',
                'username' => 'juan.delacruz',
                'phone_number' => '09890123456',
            ],
            [
                'first_name' => 'Maria',
                'last_name' => 'Santos',
                'email' => 'maria.santos@email.com',
                'username' => 'maria.santos',
                'phone_number' => '09901234567',
            ],
        ];

        foreach ($applicants as $applicant) {
            User::create([
                'first_name' => $applicant['first_name'],
                'last_name' => $applicant['last_name'],
                'middle_name' => null,
                'suffix' => null,
                'email' => $applicant['email'],
                'username' => $applicant['username'],
                'password' => Hash::make('Applicant123!@#'),
                'role' => 'applicant',
                'phone_number' => $applicant['phone_number'],
                'address' => 'Applicant Address',
                'zip_code' => '1000',
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Users seeded successfully!');
        $this->command->info('Created: 1 Admin, ' . count($staff) . ' Staff, and ' . count($applicants) . ' Applicants');
        
        // Display login credentials for testing
        $this->command->info('=====================================');
        $this->command->info('LOGIN CREDENTIALS:');
        $this->command->info('Admin - Email: admin@konstructo.com | Password: Admin123!@#');
        $this->command->info('Staff - Email: john.santos@konstructo.com | Password: Staff123!@#');
        $this->command->info('Applicant - Email: anna.lopez@email.com | Password: Applicant123!@#');
        $this->command->info('=====================================');
    }
}