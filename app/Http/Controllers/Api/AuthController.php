<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use App\Mail\OtpSendMail;
use App\Traits\ApiResponse;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;

class AuthController extends Controller
{
    use ApiResponse;
    const MAX_ATTEMPTS = 3;
    public function register(Request $request)
    {
        try {
            $existingUser = null;
            if ($request->has('email')) {
                $existingUser = User::where('email', $request->email)->first();
            }

            if ($existingUser && $existingUser->otp_verified) {
                return $this->errorResponse(['email' => ['Email is already registered and verified.']], 'Email already exists.', 422);
            }

            // Define validation rules
            $rules = [
                'first_name' => 'required|string|max:255',
                'last_name'  => 'required|string|max:255',
                'country'    => 'nullable|string|max:255',
                'password'   => 'required|string|min:6',
                'role'       => 'nullable|string',
            ];

            if ($existingUser) {
                // Email exists but pending. Validate username and phone excluding this user
                $rules['email'] = 'required|email|max:255';
                $rules['username'] = 'nullable|string|max:255|unique:users,username,' . $existingUser->id;
                $rules['phone'] = 'nullable|string|max:20|unique:users,phone,' . $existingUser->id;
            } else {
                // New email registration. Validate normally
                $rules['email'] = 'required|email|max:255|unique:users,email';
                $rules['username'] = 'nullable|string|max:255|unique:users,username';
                $rules['phone'] = 'nullable|string|max:20|unique:users,phone';
            }

            $validator = Validator::make($request->all(), $rules, [
                'email.required' => 'Email is required',
                'email.email' => 'Email must be a valid email address',
                'email.unique' => 'Email already exists',
                'password.required' => 'Password is required',
                'password.min' => 'Password must be at least 6 characters',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->toArray(), 'Validation failed', 422);
            }

            DB::beginTransaction();

            if ($existingUser) {
                $user = $existingUser;
            } else {
                $user = new User();
                $user->email = $request->email;
            }

            $user->first_name = $request->first_name;
            $user->last_name  = $request->last_name;
            $user->country    = $request->country;
            $user->username   = $request->username;
            $user->phone      = $request->phone;
            $user->password   = Hash::make($request->password);
            $user->role       = 'user'; // default role is 'user', ignore any role sent from frontend
            $user->status     = 0; // pending/inactive until OTP verified
            $user->otp_verified = false;

            // Generate OTP (following forget password Rand 100000 to 999999)
            $otp = rand(100000, 999999);
            $user->otp = $otp;
            $user->otp_attempts = 0;
            $user->otp_expired_at = Carbon::now()->addMinutes(10);

            $user->save();

            DB::commit();

            // Send OTP email
            Mail::to($user->email)->send(new OtpSendMail($otp));

            $response = [
                'email' => $user->email,
                'otp_verified' => $user->otp_verified,
                'otp_expired_at' => $user->otp_expired_at ? $user->otp_expired_at->toDateTimeString() : null,
            ];

            $message = $existingUser ? 'OTP resent successfully.' : 'Registration successful. OTP sent to your email.';
            return $this->successResponse($response, $message, 200);

        } catch (\Throwable $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            return $this->errorResponse([], 'Registration failed: ' . $e->getMessage(), 500);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->toArray(), 'Validation failed', 422);
        }

        // Attempt login
        $credentials = $request->only('email', 'password');
        $token = JWTAuth::attempt($credentials);
        if (!$token) {
            return $this->errorResponse([], 'Invalid email or password', 401);
        }

        $user = auth()->user();

        // Role-based restriction
        // if (!$user->hasRole('user')) {
        //     return $this->error([], 'Access denied. Only user accounts can login via API.', 403);
        // }

        // Allow user role
        $allowedRoles = ['user'];
        if (!in_array($user->role, $allowedRoles)) {
            return $this->errorResponse([], 'Access denied. Only user accounts can login via API.', 403);
        }

        // Check account status
        if ($user->status == 0) {
            return $this->errorResponse([], 'Your account is inactive. Please contact admin.', 403);
        }

        if (!$user->otp_verified) {
            return $this->errorResponse([], 'Please verify your OTP first.', 403);
        }


        // Update last login timestamp
        $user->last_login_at = now();
        $user->save();

        // Prepare user data for frontend
        $userData = [
            'id'         => $user->id,
            'first_name' => $user->first_name ?? '',
            'last_name'  => $user->last_name ?? '',
            'full_name'  => $user->full_name ?? '',
            'country'    => $user->country ?? '',
            'username'   => $user->username ?? '',
            'email'      => $user->email,
            'phone'      => $user->phone ?? '',
            'avatar'     => asset($user->avatar ?? 'user.jpg'),
            'token'      => $token,
            'role'       => $user->role ?? null,
        ];

        // Return success response
        return $this->successResponse($userData, 'Login successful', 200)
            ->cookie('token', $token, 1440, '/', null, request()->secure(), true, false, 'Lax');
    }

    public function logout(Request $request)
    {
        try {
            // Get token from request header
            $token = JWTAuth::getToken();

            if (!$token) {
                return $this->errorResponse([], 'Token not provided', 401);
            }

            $user = JWTAuth::authenticate($token);

            // Invalidate token
            JWTAuth::invalidate($token);

            return $this->successResponse(['full_name' => $user?->full_name,], 'Successfully logged out', 200)
                ->withoutCookie('token');
        } catch (TokenInvalidException $e) {
            return $this->errorResponse([], 'Token is invalid', 401);
        } catch (JWTException $e) {
            return $this->errorResponse([], 'Logout failed: ' . $e->getMessage(), 500);
        }
    }

    public function registerVerifyOtp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
                'otp'   => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->toArray(), 'Validation failed', 422);
            }

            $user = User::where('email', $request->email)->first();

            if (!$user || !$user->otp) {
                return $this->errorResponse([], 'Invalid OTP', 400);
            }

            // Enforce brute-force attempt limit
            if ($user->otp_attempts >= self::MAX_ATTEMPTS) {
                $user->otp = null;
                $user->otp_expired_at = null;
                $user->save();
                return $this->errorResponse([], 'Too many failed attempts. Please request a new OTP.', 429);
            }

            if ($user->otp_expired_at && Carbon::parse($user->otp_expired_at)->isPast()) {
                $user->otp = null;
                $user->otp_expired_at = null;
                $user->save();
                return $this->errorResponse([], 'OTP expired', 400);
            }

            // Verify OTP and increment attempts on failure
            if ((int) $user->otp !== (int) $request->otp) {
                $user->increment('otp_attempts');
                $remaining = self::MAX_ATTEMPTS - $user->otp_attempts;
                return $this->errorResponse([], "Invalid OTP. {$remaining} attempt(s) remaining.", 400);
            }

            // Mark OTP as verified and activate user
            $user->otp = null;
            $user->otp_expired_at = null;
            $user->otp_verified = true;
            $user->otp_verified_at = Carbon::now();
            $user->status = 1; // Active status
            $user->save();

            // Auto-assign default Standard Membership package
            $user->assignDefaultMembership();

            // Auto login (generate JWT token)
            $token = JWTAuth::fromUser($user);

            // Response Data
            $userData = [
                'id'            => $user->id,
                'first_name'    => $user->first_name,
                'last_name'     => $user->last_name,
                'full_name'     => $user->full_name,
                'country'       => $user->country ?? '',
                'username'      => $user->username ?? '',
                'email'         => $user->email,
                'phone'         => $user->phone ?? '',
                'avatar'        => asset($user->avatar ?? 'user.jpg'),
                'token'         => $token,
                'role'          => $user->role ?? null,
            ];

            return $this->successResponse($userData, 'Registration completed and logged in successfully', 200)
                ->cookie('token', $token, 1440, '/', null, request()->secure(), true, false, 'Lax');

        } catch (\Throwable $th) {
            return $this->errorResponse([], 'Failed to verify OTP: ' . $th->getMessage(), 500);
        }
    }


    //---- Forget password steps Customer start----------
    //------------------------------------------

    /**
     * Step 1: Send OTP to email or phone
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email'        => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->errorResponse([], 'User with this email does not exist.', 404);
        }

        $otp = rand(100000, 999999); // 6 digit OTP
        $expiredAt = Carbon::now()->addMinutes(10); // OTP expires in 10 minutes

        $payload = [
            'otp'            => $otp,
            'otp_attempts'   => 0,
            'otp_expired_at' => $expiredAt,
        ];

        // Do NOT clear otp_verified on an account that has already been verified.
        // Login refuses unverified accounts, so resetting the flag here let anyone
        // lock any user out of their account just by knowing their email address.
        // Only an account still awaiting its first verification is reset.
        if (!$user->otp_verified) {
            $payload['otp_verified'] = false;
        }

        $user->update($payload);

        // Send OTP via email
        Mail::to($user->email)->send(new OtpSendMail($otp));

        return $this->successResponse([
            'email' => $user->email,
            'otp_expired_at' => $user->otp_expired_at,
        ], 'OTP sent successfully.', 200);
    }

    /**
     * Step 2: Verify OTP (no email/phone needed from frontend)
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric',
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !$user->otp) {
            return $this->errorResponse([], 'Invalid OTP', 400);
        }

        // Enforce brute-force attempt limit
        if ($user->otp_attempts >= self::MAX_ATTEMPTS) {
            $user->otp = null;
            $user->otp_expired_at = null;
            $user->save();
            return $this->errorResponse([], 'Too many failed attempts. Please request a new OTP.', 429);
        }

        if ($user->otp_expired_at < Carbon::now()) {
            $user->otp = null;
            $user->otp_expired_at = null;
            $user->save();
            return $this->errorResponse([], 'OTP expired', 400);
        }

        // Verify OTP and increment attempts on failure
        if ((int) $user->otp !== (int) $request->otp) {
            $user->increment('otp_attempts');
            $remaining = self::MAX_ATTEMPTS - $user->otp_attempts;
            return $this->errorResponse([], "Invalid OTP. {$remaining} attempt(s) remaining.", 429);
        }

        $user->otp = null;
        $user->otp_expired_at = null;
        $user->otp_verified = true;
        $user->otp_verified_at                 = Carbon::now();
        $user->password_reset_token            = Str::random(64);
        $user->password_reset_token_expired_at = Carbon::now()->addMinutes(10); // 10 minutes
        $user->save();

        return $this->successResponse([
            'email' => $user->email,
            'password_reset_token' => $user->password_reset_token,
        ], 'OTP verified successfully', 200);
    }

    /**
     * Step 3: Reset password (only new password + confirmation)
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'       => 'required|email',
            'password'    => 'required|string|min:6|confirmed',
            'password_reset_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 'Error in Validation', 422);
        }

        $user = User::where('email', $request->email)->where('password_reset_token', $request->password_reset_token)->first();

        if (!$user) {
            return $this->errorResponse([], 'Invalid token or email.', 400);
        }

        if ($user->password_reset_token_expired_at < Carbon::now()) {
            return $this->errorResponse([], 'Token expired.', 400);
        }

        $user->password = Hash::make($request->password);
        $user->password_reset_token = null;
        $user->password_reset_token_expired_at = null;
        $user->save();


        // Attempt login after saving new password
        $credentials = $request->only('email', 'password');
        $token = JWTAuth::attempt($credentials);
        if (!$token) {
            return $this->errorResponse([], 'Unable to login. Please try again.', 401);
        }

        $userData = [
            'id'         => $user->id,
            'token'      => $token,
            'first_name' => $user->first_name ?? '',
            'last_name'  => $user->last_name ?? '',
            'full_name'  => $user->full_name ?? 'User_name_' . uniqid(),
            'country'    => $user->country ?? '',
            'email'      => $user->email,
            'username'   => $user->username ?? 'Username_' . uniqid(),
            'avatar'     => asset($user->avatar ?? 'user.jpg'),
        ];

        return $this->successResponse($userData, 'Password reset & login successful', 200)
            ->cookie('token', $token, 1440, '/', null, request()->secure(), true, false, 'Lax');
    }

    //---- Forget password steps Customer end----------
    //------------------------------------------

    // ------ FCM token start -----------------
    //-----------------------------------
    public function storeFcmToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string',
            'fcm_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 'Error in Validation', 422);
        }

        $user = Auth::guard('api')->user();

        // Update user table directly
        $user->update([
            'device_id' => $request->device_id,
            'fcm_token' => $request->fcm_token,
        ]);

        $response = [
            'device_id' => $user->device_id,
            'fcm_token' => $user->fcm_token,
        ];

        return $this->successResponse($response, 'FCM token stored successfully', 200);
    }


    public function deleteFcmToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 'Error in Validation', 422);
        }

        $user = Auth::guard('api')->user();

        if ($user->device_id === $request->device_id) {
            $user->update([
                'device_id' => null,
                'fcm_token' => null,
            ]);
        }

        return $this->successResponse([], 'FCM token deleted successfully', 200);
    }

    // ------ FCM token end -----------------
    //--------------------------------------
}
