<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayChanguService
{
    protected $secretKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->secretKey = config('paychangu.private_key');
        $this->baseUrl = config('paychangu.base_url', 'https://api.paychangu.com/');
    }

    /**
     * Initialize a payment and get checkout URL
     */
    public function initializePayment($data)
    {
        $payload = [
            'amount' => (float) $data['amount'],
            'currency' => $data['currency'] ?? 'MWK',
            'email' => $data['email'],
            'first_name' => $data['first_name'] ?? '',
            'last_name' => $data['last_name'] ?? '',
            'callback_url' => $data['callback_url'],
            'return_url' => $data['return_url'],
            'tx_ref' => $data['tx_ref'],
            'customization' => [
                'title' => $data['customization']['title'] ?? 'Mzuni UNITRAS Payment',
                'description' => $data['customization']['description'] ?? '',
            ],
            'meta' => $data['meta'] ?? [],
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($this->baseUrl . 'payment', $payload);

            Log::info('PayChangu API Response', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                if (isset($responseData['data']['checkout_url'])) {
                    return [
                        'success' => true,
                        'checkout_url' => $responseData['data']['checkout_url'],
                        'reference' => $responseData['data']['reference'] ?? null,
                    ];
                }
            }

            return [
                'success' => false,
                'message' => $response->json()['message'] ?? 'Payment initialization failed',
            ];

        } catch (\Exception $e) {
            Log::error('PayChangu initialization error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Payment service error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Verify a transaction
     */
    public function verifyTransaction($reference)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Accept' => 'application/json',
            ])->get($this->baseUrl . 'payment/verify/' . $reference);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'status' => $data['data']['status'] ?? 'unknown',
                    'amount' => $data['data']['amount'] ?? 0,
                    'data' => $data['data'] ?? [],
                ];
            }

            return ['success' => false, 'message' => 'Verification failed'];

        } catch (\Exception $e) {
            Log::error('PayChangu verification error: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}