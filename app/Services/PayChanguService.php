<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayChanguService
{
<<<<<<< HEAD
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
=======
    protected $baseUrl;
    protected $privateKey;
    protected $publicKey;
    protected $webhookSecret;
    protected $currency;

    /**
     * PayChangu API Endpoints - Based on your working config
     */
    const INITIATE_PAYMENT = 'payment';
    const VERIFY_PAYMENT = 'payment/verify';
    const INITIATE_PAYOUT = 'payout';
    const VERIFY_PAYOUT = 'payout/verify';

    public function __construct()
    {
        // Use your existing config keys
        $this->baseUrl = rtrim(config('services.paychangu.base_url', 'https://api.paychangu.com/'), '/');
        $this->privateKey = config('services.paychangu.private_key') ?? env('PAYCHANGU_API_PRIVATE_KEY');
        $this->publicKey = config('services.paychangu.public_key') ?? env('PAYCHANGU_PUBLIC_KEY');
        $this->webhookSecret = config('services.paychangu.webhook_secret') ?? env('PAYCHANGU_WEBHOOK_SECRET');
        $this->currency = config('services.paychangu.currency') ?? env('PAYCHANGU_CURRENCY', 'MWK');
        
        Log::info('PayChanguService initialized', [
            'base_url' => $this->baseUrl,
            'private_key_exists' => !empty($this->privateKey),
            'public_key_exists' => !empty($this->publicKey),
        ]);
    }

    /**
     * Initiate a payment
     */
    public function initiatePayment(array $data): array
    {
        try {
            if (empty($this->privateKey)) {
                throw new \Exception('PayChangu private key is not configured.');
            }

            $payload = [
                'amount' => (float) $data['amount'],
                'currency' => $data['currency'] ?? $this->currency,
                'email' => $data['email'],
                'tx_ref' => $data['reference'],
                'callback_url' => $data['callback_url'] ?? config('services.paychangu.callback_url'),
                'return_url' => $data['return_url'] ?? config('services.paychangu.return_url'),
                'first_name' => $data['first_name'] ?? auth()->user()->name ?? '',
                'last_name' => $data['last_name'] ?? '',
                'customization' => $data['customization'] ?? [
                    'title' => 'MZUNI UNITRAS Payment',
                    'description' => 'Payment for services'
                ],
                'meta' => $data['metadata'] ?? [],
            ];

            $url = $this->baseUrl . '/' . self::INITIATE_PAYMENT;

            Log::info('Initiating PayChangu payment', [
                'tx_ref' => $payload['tx_ref'],
                'amount' => $payload['amount'],
                'email' => $payload['email'],
                'url' => $url,
            ]);

            // Use private key for authentication (same as your working code)
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->privateKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($url, $payload);

            Log::info('PayChangu payment response', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('PayChangu payment initiated successfully', [
                    'tx_ref' => $data['reference'],
                    'response' => $result
                ]);
                return $result;
            }

            // Get error message from response
            $errorData = $response->json();
            $errorMessage = $errorData['message'] ?? $errorData['error'] ?? $response->body() ?? 'Payment initiation failed';

            // Log the error
            Log::error('PayChangu payment initiation failed', [
                'payload' => $payload,
                'response' => $response->body(),
                'status' => $response->status()
            ]);

            throw new \Exception($errorMessage);

        } catch (\Exception $e) {
            Log::error('PayChangu service error (initiatePayment)', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
        }
    }

    /**
<<<<<<< HEAD
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
=======
     * Initialize Payment (Alias for initiatePayment)
     */
    public function initializePayment(array $data): array
    {
        return $this->initiatePayment($data);
    }

    /**
     * Verify payment status
     */
    public function verifyPayment(string $reference): array
    {
        try {
            if (empty($this->privateKey)) {
                throw new \Exception('PayChangu private key is not configured.');
            }

            $url = $this->baseUrl . '/' . self::VERIFY_PAYMENT . '/' . $reference;

            Log::info('Verifying PayChangu payment', [
                'tx_ref' => $reference,
                'url' => $url
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->privateKey,
                'Accept' => 'application/json',
            ])->get($url);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('PayChangu payment verified', [
                    'tx_ref' => $reference,
                    'status' => $result['data']['status'] ?? 'unknown'
                ]);
                return $result;
            }

            Log::error('PayChangu payment verification failed', [
                'tx_ref' => $reference,
                'response' => $response->body(),
                'status' => $response->status()
            ]);

            throw new \Exception('Payment verification failed: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('PayChangu verification error', [
                'tx_ref' => $reference,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * 🎯 Initiate Direct Payout to Vehicle Owner
     */
    public function initiatePayout(array $data): array
    {
        try {
            if (empty($this->privateKey)) {
                throw new \Exception('PayChangu private key is not configured.');
            }

            $method = $data['method'] ?? 'mobile_money';
            
            $payload = [
                'amount' => (float) $data['amount'],
                'currency' => $data['currency'] ?? $this->currency,
                'reference' => $data['reference'],
                'narration' => $data['narration'] ?? 'MZUNI UNITRAS - Ride Payout (80%)',
                'metadata' => [
                    'booking_id' => $data['booking_id'] ?? null,
                    'owner_id' => $data['owner_id'] ?? null,
                    'payout_type' => 'ride_share_80_20',
                ]
            ];

            if ($method === 'mobile_money') {
                $payload['mobile_money'] = [
                    'provider' => $this->normalizeMobileProvider($data['mobile_provider'] ?? 'airtel_money'),
                    'number' => $this->normalizePhoneNumber($data['mobile_number']),
                    'name' => $data['account_name'] ?? null,
                ];
            } else {
                $payload['bank'] = [
                    'bank_name' => $data['bank_name'],
                    'account_number' => $data['account_number'],
                    'account_name' => $data['account_name'],
                    'branch' => $data['branch'] ?? null,
                ];
            }

            $url = $this->baseUrl . '/' . self::INITIATE_PAYOUT;

            Log::info('Initiating direct payout', [
                'owner_id' => $data['owner_id'],
                'amount' => $data['amount'],
                'method' => $method,
                'reference' => $data['reference'],
                'url' => $url
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->privateKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($url, $payload);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('Payout initiated successfully', [
                    'reference' => $data['reference'],
                    'response' => $result
                ]);
                return $result;
            }

            Log::error('Payout initiation failed', [
                'payload' => $payload,
                'response' => $response->body(),
                'status' => $response->status()
            ]);

            throw new \Exception('Payout initiation failed: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('PayChangu payout error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Verify payout status
     */
    public function verifyPayout(string $reference): array
    {
        try {
            $url = $this->baseUrl . '/' . self::VERIFY_PAYOUT . '/' . $reference;

            Log::info('Verifying payout status', [
                'reference' => $reference,
                'url' => $url
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->privateKey,
                'Accept' => 'application/json',
            ])->get($url);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('Payout verification response', [
                    'reference' => $reference,
                    'status' => $result['status'] ?? 'unknown'
                ]);
                return $result;
            }

            Log::error('Payout verification failed', [
                'reference' => $reference,
                'response' => $response->body(),
                'status' => $response->status()
            ]);

            throw new \Exception('Payout verification failed: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('PayChangu payout verification error', [
                'reference' => $reference,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Verify webhook signature
     */
    public function verifyWebhookSignature($payload, $signature): bool
    {
        if (empty($signature) || empty($this->webhookSecret)) {
            Log::warning('Webhook signature or secret missing');
            return false;
        }

        $expectedSignature = hash_hmac('sha256', $payload, $this->webhookSecret);
        $isValid = hash_equals($expectedSignature, $signature);

        if (!$isValid) {
            Log::warning('Invalid webhook signature', [
                'expected' => $expectedSignature,
                'received' => $signature
            ]);
        }

        return $isValid;
    }

    /**
     * Process webhook payload
     */
    public function processWebhook(array $payload): array
    {
        $reference = $payload['tx_ref'] ?? $payload['reference'] ?? null;
        $status = $payload['status'] ?? null;
        $amount = $payload['amount'] ?? 0;
        $currency = $payload['currency'] ?? $this->currency;
        $metadata = $payload['meta'] ?? $payload['metadata'] ?? [];

        return [
            'reference' => $reference,
            'status' => $status,
            'amount' => (float) $amount,
            'currency' => $currency,
            'is_successful' => in_array($status, ['completed', 'success', 'successful']),
            'is_failed' => in_array($status, ['failed', 'cancelled']),
            'metadata' => $metadata,
            'raw_payload' => $payload
        ];
    }

    /**
     * ============================================================
     * HELPER METHODS
     * ============================================================
     */

    protected function normalizeMobileProvider(string $provider): string
    {
        $providers = [
            'airtel' => 'airtel_money',
            'airtel_money' => 'airtel_money',
            'tnm' => 'tnm_mpamba',
            'tnm_mpamba' => 'tnm_mpamba',
        ];

        return $providers[strtolower($provider)] ?? 'airtel_money';
    }

    protected function normalizePhoneNumber(string $number): string
    {
        $number = preg_replace('/[^0-9]/', '', $number);
        
        if (strlen($number) > 9 && substr($number, 0, 1) === '0') {
            $number = substr($number, 1);
        }
        
        if (strlen($number) === 9) {
            $number = '0' . $number;
        }
        
        return $number;
    }

    /**
     * Format amount for display
     */
    public function formatAmount($amount): string
    {
        return $this->currency . ' ' . number_format((float) $amount, 2);
    }

    /**
     * Get user-friendly error message
     */
    public function getErrorMessage(\Exception $e): string
    {
        $message = $e->getMessage();
        
        $errorMap = [
            'Could not resolve host' => 'Unable to connect to PayChangu. Please check your internet connection.',
            'cURL error 6' => 'Network connection issue. Please check your internet connection.',
            'Unauthorized' => 'Invalid API key. Please check your credentials.',
            'insufficient balance' => 'Insufficient balance. Please top up your account.',
            'invalid reference' => 'Invalid transaction reference.',
            'payout failed' => 'Payout failed. Please contact support.',
            '404' => 'Endpoint not found. Please check API URL.',
            '500' => 'Server error. Please try again later.',
            'Connection timed out' => 'Connection timed out. Please try again.',
        ];

        foreach ($errorMap as $key => $friendlyMessage) {
            if (stripos($message, $key) !== false) {
                return $friendlyMessage;
            }
        }

        return $message;
    }
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
}