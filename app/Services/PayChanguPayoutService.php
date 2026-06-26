<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayChanguPayoutService
{
    protected $secretKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->secretKey = config('paychangu.secret_key');
        $this->baseUrl = config('paychangu.base_url', 'https://api.paychangu.com');
    }

    /**
     * Send money to a mobile wallet.
     */
    public function sendMoney($amount, $phoneNumber, $reference, $description = '')
    {
        try {
            $payload = [
                'amount' => (float) $amount,
                'currency' => 'MWK',
                'phone' => $this->formatPhoneNumber($phoneNumber),
                'reference' => $reference,
                'description' => $description,
                'provider' => 'airtel_money',
            ];

            Log::info('Initiating payout', ['payload' => $payload]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($this->baseUrl . '/payouts/mobile-money', $payload);

            $data = $response->json();

            Log::info('Payout response', ['response' => $data]);

            if ($response->successful() && isset($data['success']) && $data['success'] === true) {
                return [
                    'success' => true,
                    'reference' => $data['data']['reference'] ?? $reference,
                    'message' => $data['message'] ?? 'Payout successful',
                ];
            }

            return [
                'success' => false,
                'message' => $data['message'] ?? 'Payout failed',
            ];
        } catch (\Exception $e) {
            Log::error('Payout error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Format phone number to international format.
     */
    protected function formatPhoneNumber($phone): string
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