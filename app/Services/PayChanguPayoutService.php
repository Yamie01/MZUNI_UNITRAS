<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayChanguPayoutService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('paychangu.api_key');
        $this->baseUrl = config('paychangu.base_url', 'https://api.paychangu.com');
    }

    /**
     * Send money to a mobile wallet.
     */
    public function sendMoney($amount, $phone, $reference, $narration = 'Ride earnings')
{
    $payload = [
        'amount' => (float) $amount,
        'phone' => $this->formatPhoneNumber($phone),
        'reference' => $reference,
        'narration' => $narration,
        'provider' => 'airtel_money', // or 'tnm_mpamba'
    ];

    Log::info('Initiating payout', ['payload' => $payload]); // ✅ FIXED: array context

    try {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . '/payout', $payload);

        $body = $response->json();

        Log::info('Payout response', ['response' => $body]); // ✅ FIXED: array context

        if ($response->successful() && ($body['success'] ?? false)) {
            return [
                'success' => true,
                'reference' => $body['data']['reference'] ?? null,
                'message' => $body['message'] ?? 'Payout successful',
            ];
        }

        return [
            'success' => false,
            'message' => $body['message'] ?? 'Payout failed',
        ];
    } catch (\Exception $e) {
        Log::error('Payout exception: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => $e->getMessage(),
        ];
    }
}
    protected function formatPhoneNumber($phone)
    {
        // Remove any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Remove leading 0 if present
        if (substr($phone, 0, 1) === '0') {
            $phone = substr($phone, 1);
        }

        // Ensure it starts with 265 (Malawi country code)
        if (substr($phone, 0, 3) !== '265') {
            $phone = '265' . $phone;
        }

        return $phone;
    }
}