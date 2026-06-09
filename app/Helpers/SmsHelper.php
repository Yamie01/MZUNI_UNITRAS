<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsHelper
{
    /**
     * Send SMS using Africa's Talking API
     */
    public static function send($phoneNumber, $message)
    {
        // Clean phone number (remove spaces and non-digits)
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Add country code if missing (Malawi is 265)
        if (strlen($phoneNumber) === 10) {
            $phoneNumber = '265' . $phoneNumber;
        }
        
        // Ensure number starts with 265
        if (!str_starts_with($phoneNumber, '265')) {
            $phoneNumber = '265' . ltrim($phoneNumber, '0');
        }
        
        // Africa's Talking API endpoint
        $url = 'https://api.africastalking.com/version1/messaging';
        
        // Prepare the request data
        $data = [
            'username' => env('AFRICASTALKING_USERNAME', 'sandbox'),
            'to' => $phoneNumber,
            'message' => $message,
            'from' => env('AFRICASTALKING_FROM', 'MzuniUNITRAS'),
        ];
        
        try {
            $response = Http::withHeaders([
                'apiKey' => env('AFRICASTALKING_API_KEY'),
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Accept' => 'application/json',
            ])->asForm()->post($url, $data);
            
            if ($response->successful()) {
                $result = $response->json();
                Log::info('SMS sent successfully', ['to' => $phoneNumber, 'response' => $result]);
                return true;
            } else {
                Log::error('SMS sending failed', ['to' => $phoneNumber, 'response' => $response->body()]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('SMS exception: ' . $e->getMessage());
            return false;
        }
    }
}