<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtpAuthController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    public function requestOtp(Request $request)
    {
        $request->validate([
            'whatsapp_number' => 'required|string|exists:users'
        ]);

        $otp = $this->otpService->generateOtp($request->whatsapp_number);

        return response()->json([
            'message' => 'OTP sent to WhatsApp number',
            'expires_in' => config('otp.expiry', 5) . ' minutes'
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'whatsapp_number' => 'required|string',
            'otp' => 'required|string|digits:6'
        ]);

        if ($this->otpService->verifyOtp($request->whatsapp_number, $request->otp)) {
            $user = User::where('whatsapp_number', $request->whatsapp_number)->first();

            Auth::login($user);

            $token = $user->createToken('royal-gam-otp-token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'user' => $user,
                'token' => $token
            ]);
        }

        return response()->json([
            'message' => 'Invalid OTP or expired'
        ], 401);
    }
}
