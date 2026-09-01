<?php

namespace App\Traits;

use App\Models\User;
use App\Notifications\OtpNotification;
use Illuminate\Support\Facades\Hash;

trait ApiResponse
{
    public function successResponse($data, $message = null, $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'code' => $code,
        ], $code);
    }

    public function errorResponse($data, $message = null, $code = 500)
    {
        if ($code === 422 && is_array($data) && !empty($data)) {
            $firstFieldErrors = reset($data);
            if (is_array($firstFieldErrors) && !empty($firstFieldErrors)) {
                $message = reset($firstFieldErrors);
            } elseif (is_string($firstFieldErrors)) {
                $message = $firstFieldErrors;
            }
        }

        return response()->json([
            'status' => false,
            'message' => $message,
            'data' => $data,
            'code' => $code,
        ], $code);
    }

    public function generateOtp(User $user)
    {
        $otp = rand(1000, 9999);
        $user->otp = Hash::make($otp);
        $user->otp_created_at = now();

        $user->notify(new OtpNotification($otp));

        $user->save();

        return response()->json(['message' => 'OTP sent to your email!']);
    }
}
