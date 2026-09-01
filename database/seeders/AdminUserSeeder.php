<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Users
        $users = [
            [
                'first_name' => 'System',
                'last_name' => 'Admin',
                'full_name' => 'System Admin',
                'country' => 'United States',
                'username' => 'admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('12345678'),
                'role' => 'admin',
                'otp_verified' => 1,
            ],
            [
                'first_name' => 'Manager',
                'last_name' => 'One',
                'full_name' => 'Manager One',
                'country' => 'United States',
                'username' => 'manager1',
                'email' => 'manager1@gmail.com',
                'password' => Hash::make('12345678'),
                'role' => 'manager',
                'otp_verified' => 1,
            ],
            [
                'first_name' => 'Manager',
                'last_name' => 'Two',
                'full_name' => 'Manager Two',
                'country' => 'United States',
                'username' => 'manager2',
                'email' => 'manager2@gmail.com',
                'password' => Hash::make('12345678'),
                'role' => 'manager',
                'otp_verified' => 1,
            ],
            [
                'first_name' => 'Manager',
                'last_name' => 'Three',
                'full_name' => 'Manager Three',
                'country' => 'United States',
                'username' => 'manager3',
                'email' => 'manager3@gmail.com',
                'password' => Hash::make('12345678'),
                'role' => 'manager',
                'otp_verified' => 1,
            ],
            [
                'first_name' => 'User',
                'last_name' => 'One',
                'full_name' => 'User One',
                'country' => 'United States',
                'username' => 'user1',
                'email' => 'user1@gmail.com',
                'password' => Hash::make('12345678'),
                'role' => 'user',
                'otp_verified' => 1,
            ],
            [
                'first_name' => 'User',
                'last_name' => 'Two',
                'full_name' => 'User Two',
                'country' => 'United States',
                'username' => 'user2',
                'email' => 'user2@gmail.com',
                'password' => Hash::make('12345678'),
                'role' => 'user',
                'otp_verified' => 1,
            ],
            [
                'first_name' => 'User',
                'last_name' => 'Three',
                'full_name' => 'User Three',
                'country' => 'United States',
                'username' => 'user3',
                'email' => 'user3@gmail.com',
                'password' => Hash::make('12345678'),
                'role' => 'user',
                'otp_verified' => 1,
            ],

        ];

        foreach ($users as $user) {
            $createdUser = User::updateOrCreate(
                ['email' => $user['email']], // condition
                $user                        // data to update or insert
            );

            if (($createdUser->role ?? 'user') === 'user') {
                $createdUser->assignDefaultMembership();
            }
        }
    }
}
