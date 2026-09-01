<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('api login allows user role', function () {
    $user = User::factory()->create([
        'email' => 'user@example.com',
        'password' => Hash::make('password123'),
        'role' => 'user',
        'otp_verified' => true,
        'status' => 1,
    ]);

    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
             ->assertJsonFragment([
                 'email' => $user->email,
                 'role' => 'user',
             ]);
});

test('api login denies non-user roles', function () {
    $roles = ['admin', 'editor', 'moderator', 'standard_member', 'premium_member'];

    foreach ($roles as $role) {
        $user = User::factory()->create([
            'email' => $role . '@example.com',
            'password' => Hash::make('password123'),
            'role' => $role,
            'otp_verified' => true,
            'status' => 1,
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(403)
                 ->assertJsonFragment([
                     'message' => 'Access denied. Only user accounts can login via API.',
                 ]);
    }
});
