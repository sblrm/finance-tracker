<?php

namespace App\Jobs;

use App\Services\GeminiReceiptService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProcessReceiptExtraction implements ShouldQueue
{
    use Queueable;

    public $timeout = 120; // 2 minutes timeout for job

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $imagePath,
        public string $sessionKey
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(GeminiReceiptService $ai): void
    {
        try {
            $absolutePath = Storage::disk('public')->path($this->imagePath);
            $result = $ai->extract($absolutePath);
            
            // Store result in cache/session for retrieval
            cache()->put("receipt_extraction_{$this->sessionKey}", [
                'status' => 'completed',
                'data' => $result,
                'image_path' => $this->imagePath
            ], now()->addMinutes(10));
            
        } catch (\Exception $e) {
            Log::error('Receipt extraction failed', [
                'image_path' => $this->imagePath,
                'error' => $e->getMessage()
            ]);
            
            cache()->put("receipt_extraction_{$this->sessionKey}", [
                'status' => 'failed',
                'error' => 'AI processing failed. Please try manual entry.',
                'image_path' => $this->imagePath
            ], now()->addMinutes(10));
        }
    }
}
