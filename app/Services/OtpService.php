<?php

namespace App\Services;

use App\Models\OtpVerification;
use Carbon\Carbon;
use App\Services\WhatsAppService;

class OtpService
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    public function generateOtp($whatsappNumber)
    {
        // Delete any existing OTPs
        OtpVerification::where('whatsapp_number', $whatsappNumber)->delete();

        // Generate 6-digit OTP
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
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

    public function verifyOtp($whatsappNumber, $otp)
    {
        $record = OtpVerification::where('whatsapp_number', $whatsappNumber)
            ->where('otp', $otp)
            ->where('expires_at', '>', now())
            ->first();

        if ($record) {
            $record->update(['verified' => true]);
            return true;
        }

        return false;
    }
}
