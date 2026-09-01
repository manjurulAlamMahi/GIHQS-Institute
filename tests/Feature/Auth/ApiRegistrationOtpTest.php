<?php

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpSendMail;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

test('new API registration creates user and sends OTP', function () {
    Mail::fake();

    $response = $this->postJson('/api/register', [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'country' => 'USA',
        'username' => 'johndoe',
        'email' => 'john@example.com',
        'phone' => '1234567890',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
             ->assertJsonStructure([
                 'success',
                 'message',
                 'data' => [
                     'email',
                     'otp_verified',
                     'otp_expired_at'
                 ]
             ])
             ->assertJsonMissing(['otp']);

    $user = User::where('email', 'john@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->otp_verified)->toBeFalse();
    expect($user->status)->toBe(0);

    Mail::assertSent(OtpSendMail::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email) && $mail->otp == $user->otp;
    });
});

test('API registration of pending user updates details and resends OTP', function () {
    Mail::fake();

    // Create a pending user
    $user = User::factory()->create([
        'first_name' => 'OldName',
        'email' => 'pending@example.com',
        'otp_verified' => false,
        'status' => 0,
    ]);

    $response = $this->postJson('/api/register', [
        'first_name' => 'NewName',
        'last_name' => 'Doe',
        'country' => 'USA',
        'username' => 'pendinguser',
        'email' => 'pending@example.com',
        'phone' => '0987654321',
        'password' => 'newpassword123',
    ]);

    $response->assertStatus(200);

    $user->refresh();
    expect($user->first_name)->toBe('NewName');
    expect($user->otp_verified)->toBeFalse();
    expect(Hash::check('newpassword123', $user->password))->toBeTrue();

    Mail::assertSent(OtpSendMail::class);
});

test('API registration of verified user is blocked', function () {
    Mail::fake();

    // Create a verified user
    User::factory()->create([
        'email' => 'verified@example.com',
        'otp_verified' => true,
        'status' => 1,
    ]);

    $response = $this->postJson('/api/register', [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'country' => 'USA',
        'username' => 'verifieduser',
        'email' => 'verified@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(422)
             ->assertJsonFragment([
                 'email' => ['Email is already registered and verified.']
             ]);
});

test('API registration OTP verification activates user and logs them in', function () {
    $user = User::factory()->create([
        'email' => 'verify@example.com',
        'otp' => '123456',
        'otp_verified' => false,
        'otp_expired_at' => Carbon::now()->addMinutes(10),
        'status' => 0,
    ]);

    $response = $this->postJson('/api/register/verify-otp', [
        'email' => 'verify@example.com',
        'otp' => '123456',
    ]);

    $response->assertStatus(200)
             ->assertJsonStructure([
                 'success',
                 'message',
                 'data' => [
                     'id',
                     'first_name',
                     'last_name',
                     'email',
                     'token'
                 ]
             ]);

    $user->refresh();
    expect($user->otp_verified)->toBeTrue();
    expect($user->status)->toBe(1);
    expect($user->otp)->toBeNull();
});

test('API login fails for unverified users', function () {
    $user = User::factory()->create([
        'email' => 'unverified@example.com',
        'password' => Hash::make('password123'),
        'otp_verified' => false,
        'status' => 1,
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'unverified@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(403)
             ->assertJsonFragment(['message' => 'Please verify your OTP first.']);
});
