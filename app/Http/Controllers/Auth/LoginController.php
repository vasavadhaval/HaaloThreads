<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OtpVerification;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Request OTP for login
     */
    public function requestOtp(Request $request)
    {
        try {
            $request->validate([
                'whatsapp_number' => ['required', 'string', 'max:20']
            ]);

            // Check if user exists and is verified
            $user = User::where('whatsapp_number', $request->whatsapp_number)
                      ->whereNotNull('whatsapp_verified_at')
                      ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'No verified account found with this WhatsApp number',
                    'errors' => [
                        'whatsapp_number' => ['Please register first']
                    ]
                ], 404);
            }

            // Generate and send OTP
            $otp = $this->generateAndSendOtp($user->whatsapp_number);

            return response()->json([
                'success' => true,
                'message' => 'OTP sent to WhatsApp number',
                'data' => [
                    'user_id' => $user->id,
                    'whatsapp_number' => $user->whatsapp_number,
                    'otp_expires_in' => config('otp.expiry', 5) . ' minutes'
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('OTP Request Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify OTP and login
     */
    public function verifyOtp(Request $request)
    {
        try {
            $request->validate([
                'user_id' => ['required', 'exists:users,id'],
                'whatsapp_number' => ['required', 'string', 'max:20'],
                'otp' => ['required', 'string', 'digits:6']
            ]);

            $user = User::findOrFail($request->user_id);

            // Verify user-number match
            if ($user->whatsapp_number !== $request->whatsapp_number) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid user and WhatsApp number combination',
                    'errors' => [
                        'whatsapp_number' => ['Number does not match user record']
                    ]
                ], 400);
            }

            // Verify OTP
            $otpRecord = OtpVerification::where('whatsapp_number', $user->whatsapp_number)
                ->where('otp', $request->otp)
                ->where('expires_at', '>', Carbon::now())
                ->first();

            if (!$otpRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'OTP verification failed',
                    'errors' => [
                        'otp' => ['Invalid or expired OTP']
                    ]
                ], 401);
            }

            // Delete used OTP
            $otpRecord->delete();

            // Generate auth token
            $token = $user->createToken('royal-gam-login-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('OTP Verification Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Login failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Password login (optional)
     */
    public function passwordLogin(Request $request)
    {
        try {
            $request->validate([
                'whatsapp_number' => ['required', 'string'],
                'password' => ['required', 'string']
            ]);

            $user = User::where('whatsapp_number', $request->whatsapp_number)
                      ->whereNotNull('whatsapp_verified_at')
                      ->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials',
                    'errors' => [
                        'whatsapp_number' => ['Invalid WhatsApp number or password']
                    ]
                ], 401);
            }

            $token = $user->createToken('royal-gam-password-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Shared OTP generation method
     */
    protected function generateAndSendOtp($whatsappNumber)
    {
        // Delete any existing OTPs for this number
        OtpVerification::where('whatsapp_number', $whatsappNumber)->delete();

        // Generate 6-digit OTP (use random in production)
        $otp = 123456; // For testing: str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT)
        $expiresAt = Carbon::now()->addMinutes(config('otp.expiry', 5));

        // Store OTP
        OtpVerification::create([
            'whatsapp_number' => $whatsappNumber,
            'otp' => $otp,
            'expires_at' => $expiresAt
        ]);

        // Send via WhatsApp
        $this->whatsappService->sendOtp($whatsappNumber, $otp);

        return $otp;
    }
}
