<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OtpVerification;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Carbon\Carbon;

class RegisterController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'username' => ['required', 'string', 'max:255'],
                'first_name' => ['required', 'string', 'max:255'],
                'surname' => ['required', 'string', 'max:255'],
                'father_name' => ['nullable', 'string', 'max:255'],
                'email' => ['nullable', 'email', 'max:255'],
                'whatsapp_number' => ['required', 'string', 'max:20'],
                'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            ]);

            // Check if number is already verified
            if (User::where('whatsapp_number', $validated['whatsapp_number'])
                ->whereNotNull('whatsapp_verified_at')
                ->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This WhatsApp number is already registered',
                    'errors' => [
                        'whatsapp_number' => ['This number is already verified']
                    ]
                ], 409);
            }

            DB::beginTransaction();

            // Clean up any existing unverified users
            User::where('whatsapp_number', $validated['whatsapp_number'])
                ->whereNull('whatsapp_verified_at')
                ->delete();

            $user = User::create([
                'username' => $validated['username'],
                'first_name' => $validated['first_name'],
                'surname' => $validated['surname'],
                'father_name' => $validated['father_name'] ?? null,
                'email' => $validated['email'] ?? null,
                'whatsapp_number' => $validated['whatsapp_number'],
                'password' => isset($validated['password']) ? Hash::make($validated['password']) : null,
                'whatsapp_verified_at' => null,
            ]);

            $this->generateAndSendOtp($user->whatsapp_number);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully',
                'data' => [
                    'user_id' => $user->id,
                    'whatsapp_number' => $user->whatsapp_number,
                    'otp_expires_in' => config('otp.expiry', 5) . ' minutes'
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

   public function verifyOtpAndCompleteRegistration(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => ['required', 'exists:users,id'],
                'whatsapp_number' => ['required', 'string'],
                'otp' => ['required', 'string', 'digits:6'],
            ]);

            DB::beginTransaction();

            $user = User::findOrFail($validated['user_id']);

            // Verify user-number match
            if ($user->whatsapp_number !== $validated['whatsapp_number']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid user and WhatsApp number combination',
                    'errors' => [
                        'whatsapp_number' => ['The provided number does not match user records']
                    ]
                ], 400);
            }

            // Check if already verified
            if ($user->whatsapp_verified_at) {
                return response()->json([
                    'success' => true,
                    'message' => 'Account is already verified',
                    'data' => [
                        'user' => $user,
                        'verified_at' => $user->whatsapp_verified_at
                    ]
                ], 200);
            }

            // Verify OTP
            $otpRecord = OtpVerification::where('whatsapp_number', $user->whatsapp_number)
                ->where('otp', $validated['otp'])
                ->where('expires_at', '>', Carbon::now())
                ->first();

            if (!$otpRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'OTP verification failed',
                    'errors' => [
                        'otp' => ['The OTP is invalid or has expired']
                    ]
                ], 401);
            }

            // Check for race condition
            if (User::where('whatsapp_number', $user->whatsapp_number)
                ->whereNotNull('whatsapp_verified_at')
                ->where('id', '!=', $user->id)
                ->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Number verification conflict',
                    'errors' => [
                        'whatsapp_number' => ['This number was verified by another account']
                    ]
                ], 409);
            }

            // Mark as verified
            // $user->update(['whatsapp_verified_at' => Carbon::now()]);
            $user->whatsapp_verified_at = Carbon::now();
            $user->save();

            OtpVerification::where('whatsapp_number', $user->whatsapp_number)->delete();
            $token = $user->createToken('royal-gam-token')->plainTextToken;

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Registration completed successfully',
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
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'OTP verification failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    protected function generateAndSendOtp($whatsappNumber)
    {
        try {
            OtpVerification::where('whatsapp_number', $whatsappNumber)->delete();

            $otp = 123456; // For testing - replace with: str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT)
            $expiresAt = Carbon::now()->addMinutes(config('otp.expiry', 5));

            OtpVerification::create([
                'whatsapp_number' => $whatsappNumber,
                'otp' => $otp,
                'expires_at' => $expiresAt
            ]);

            $this->whatsappService->sendOtp($whatsappNumber, $otp);

            return $otp;

        } catch (\Exception $e) {
            throw new \Exception("Failed to generate OTP: " . $e->getMessage());
        }
    }

    public function resendOtp(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => ['required', 'exists:users,id'],
                'whatsapp_number' => ['required', 'string'],
            ]);

            $user = User::findOrFail($validated['user_id']);

            if ($user->whatsapp_verified_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account already verified',
                    'errors' => [
                        'user' => ['This account is already verified']
                    ]
                ], 400);
            }

            if ($user->whatsapp_number !== $validated['whatsapp_number']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid WhatsApp number',
                    'errors' => [
                        'whatsapp_number' => ['Number does not match user record']
                    ]
                ], 400);
            }

            $otp = $this->generateAndSendOtp($user->whatsapp_number);

            return response()->json([
                'success' => true,
                'message' => 'New OTP sent successfully',
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
            return response()->json([
                'success' => false,
                'message' => 'Failed to resend OTP',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
