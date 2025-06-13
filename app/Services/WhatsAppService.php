<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $apiUrl;
    protected $token;

    public function __construct()
    {
        $this->apiUrl = 'https://graph.facebook.com/' .
                       env('WHATSAPP_API_VERSION', 'v18.0') . '/' .
                       env('WHATSAPP_BUSINESS_ID') . '/messages';
        $this->token = env('WHATSAPP_BUSINESS_TOKEN');
    }

    public function sendOtp($toMobileNumber, $otp)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl, [
                "messaging_product" => "whatsapp",
                "recipient_type" => "individual",
                "to" => $toMobileNumber,
                "type" => "template",
                "template" => [
                    "name" => "authentication_code_copy_code_button",
                    "language" => ["code" => "en_US"],
                    "components" => [
                        [
                            "type" => "body",
                            "parameters" => [
                                ["type" => "text", "text" => $otp]
                            ]
                        ],
                        [
                            "type" => "button",
                            "sub_type" => "url",
                            "index" => "0",
                            "parameters" => [
                                ["type" => "text", "text" => $otp]
                            ]
                        ]
                    ]
                ]
            ]);
            // print_r($response->body());
            // exit;
            if ($response->successful()) {
                return true;
            }

            Log::error('WhatsApp API Error: ' . $response->body());
            return false;

        } catch (\Exception $e) {
            Log::error('WhatsApp Service Exception: ' . $e->getMessage());
            return false;
        }
    }
}
