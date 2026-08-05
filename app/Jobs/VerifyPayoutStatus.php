<?php

namespace App\Jobs;

use App\Models\Payout;
use App\Services\PayChanguService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class VerifyPayoutStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $payoutId;
    public $tries = 5;
    public $backoff = [5, 10, 30, 60, 120]; // Retry delays in minutes

    public function __construct($payoutId)
    {
        $this->payoutId = $payoutId;
    }

    public function handle(PayChanguService $payChanguService)
    {
        $payout = Payout::find($this->payoutId);
        
        if (!$payout || $payout->isCompleted()) {
            return;
        }

        try {
            Log::info('🔍 Verifying payout status', [
                'payout_id' => $payout->id,
                'reference' => $payout->reference
            ]);

            $status = $payChanguService->verifyPayout($payout->reference);
            
            if ($status['status'] === 'completed') {
                // Payout completed
                $payout->markAsCompleted($status);
                
                Log::info('✅ Payout completed successfully', [
                    'payout_id' => $payout->id,
                    'reference' => $payout->reference
                ]);
            } elseif ($status['status'] === 'failed') {
                // Payout failed
                $payout->markAsFailed($status['message'] ?? 'Payout failed', $status);
                
                Log::error('❌ Payout failed', [
                    'payout_id' => $payout->id,
                    'reference' => $payout->reference,
                    'response' => $status
                ]);
            } else {
                // Still processing, retry later
                Log::info('⏳ Payout still processing, retrying...', [
                    'payout_id' => $payout->id,
                    'status' => $status['status'] ?? 'unknown'
                ]);
                $this->release(now()->addMinutes(5));
            }
        } catch (\Exception $e) {
            Log::error('Payout verification failed', [
                'payout_id' => $payout->id,
                'error' => $e->getMessage()
            ]);
            $this->release(now()->addMinutes(10));
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::critical('Payout verification job failed permanently', [
            'payout_id' => $this->payoutId,
            'error' => $exception->getMessage()
        ]);

        $payout = Payout::find($this->payoutId);
        if ($payout && !$payout->isCompleted()) {
            $payout->markAsFailed('Verification job failed: ' . $exception->getMessage());
        }
    }
}