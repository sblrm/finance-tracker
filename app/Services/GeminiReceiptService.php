<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Str;

class GeminiReceiptService
{
    public function __construct(
        private ?string $apiKey = null,
        private ?string $model = null,
        private ?Client $http = null,
    ) {
        $this->apiKey = $this->apiKey ?? config('services.gemini.key', env('GEMINI_API_KEY'));
        $this->model  = $this->model  ?? config('services.gemini.model', env('GEMINI_MODEL', 'gemini-1.5-flash'));
        $this->http   = $this->http   ?? new Client([
            'base_uri'        => 'https://generativelanguage.googleapis.com/v1beta/',
            'timeout'         => 180,  // Increased to 3 minutes for large images
            'connect_timeout' => 20,   // Increased connection timeout
            'read_timeout'    => 180,  // Explicit read timeout
        ]);
    }

    public function extract(string $absoluteImagePath): array
    {
        // Optimize image size before sending to AI
        $imageBytes = $this->optimizeImageForAI($absoluteImagePath);

        $prompt = [
            "role" => "user",
            "parts" => [
                ["text" => "Extract receipt data as JSON only. Format: {\"merchant\":\"store name\",\"datetime\":\"YYYY-MM-DD HH:mm:ss\",\"total\":amount,\"currency\":\"IDR\",\"items\":[{\"name\":\"item\",\"qty\":1,\"unit_price\":price,\"subtotal\":subtotal}]}. No explanation needed."],
                ["inline_data" => ["mime_type" => "image/jpeg", "data" => $imageBytes]],
            ],
        ];

        try {
            $response = $this->postWithRetry(
                sprintf('models/%s:generateContent', $this->model),
                [
                    'query' => ['key' => $this->apiKey],
                    'json'  => ['contents' => [$prompt]],
                ],
                2
            );
        } catch (ConnectException $e) {
            throw new \RuntimeException('Tidak bisa terhubung ke layanan AI. Coba lagi beberapa saat.');
        } catch (RequestException $e) {
            $errno = $e->getHandlerContext()['errno'] ?? null;
            $statusCode = $e->getResponse()?->getStatusCode();
            
            // Check for various timeout scenarios
            if ($errno === 28 || $errno === 7 || $statusCode === 408 || str_contains($e->getMessage(), 'timeout')) {
                throw new \RuntimeException('Proses AI timeout (gambar mungkin terlalu besar). Coba kompres gambar atau isi manual.');
            }
            
            if ($statusCode === 429) {
                throw new \RuntimeException('Terlalu banyak permintaan ke AI. Tunggu sebentar lalu coba lagi.');
            }
            
            if ($statusCode >= 500) {
                throw new \RuntimeException('Server AI sedang bermasalah. Coba lagi nanti atau isi manual.');
            }
            
            throw new \RuntimeException('Permintaan AI gagal: ' . ($e->getMessage() ?: 'unknown'));
        }

        $data = json_decode($response->getBody()->getContents(), true);
        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
        $text = Str::of(trim($text))->replace(['```json', '```'], '')->trim();
        $json = json_decode((string) $text, true);

        if (!is_array($json)) {
            return ['merchant' => null, 'datetime' => null, 'total' => null, 'currency' => null, 'items' => []];
        }

        $json['items'] = array_values(array_map(function ($it) {
            return [
                'name'       => $it['name']       ?? 'Item',
                'qty'        => (float)($it['qty'] ?? 1),
                'unit_price' => isset($it['unit_price']) ? (float)$it['unit_price'] : null,
                'subtotal'   => isset($it['subtotal'])   ? (float)$it['subtotal']   : null,
            ];
        }, $json['items'] ?? []));

        $json['total']    = isset($json['total']) ? (float)$json['total'] : null;
        $json['merchant'] = $json['merchant'] ?? null;
        $json['currency'] = $json['currency'] ?? null;
        $json['datetime'] = $json['datetime'] ?? null;

        return $json;
    }

    private function postWithRetry(string $uri, array $options, int $maxRetry = 1)
    {
        $attempt = 0;
        beginning:
        try {
            return $this->http->post($uri, $options);
        } catch (ConnectException $e) {
            if ($attempt++ < $maxRetry) {
                usleep(1_000_000); // 1s delay for connection issues
                goto beginning;
            }
            throw $e;
        } catch (RequestException $e) {
            // Don't retry on timeout errors or client errors (4xx)
            $statusCode = $e->getResponse()?->getStatusCode();
            $isTimeout = str_contains($e->getMessage(), 'timeout') || 
                        $e->getHandlerContext()['errno'] === 28;
            
            if (!$isTimeout && $statusCode >= 500 && $attempt++ < $maxRetry) {
                usleep(2_000_000); // 2s delay for server errors
                goto beginning;
            }
            throw $e;
        }
    }

    /**
     * Optimize image size for AI processing to reduce timeout risk
     */
    private function optimizeImageForAI(string $absoluteImagePath): string
    {
        // Check file size first
        $fileSize = filesize($absoluteImagePath);
        $maxSize = 4 * 1024 * 1024; // 4MB limit for better performance
        
        if ($fileSize <= $maxSize) {
            // File is already small enough, return as-is
            return base64_encode(file_get_contents($absoluteImagePath));
        }
        
        // If file is too large, try to compress it
        $imageInfo = getimagesize($absoluteImagePath);
        if (!$imageInfo) {
            // If can't get image info, just return original
            return base64_encode(file_get_contents($absoluteImagePath));
        }
        
        $originalWidth = $imageInfo[0];
        $originalHeight = $imageInfo[1];
        $mimeType = $imageInfo['mime'];
        
        // Create image resource based on type
        $sourceImage = match($mimeType) {
            'image/jpeg' => imagecreatefromjpeg($absoluteImagePath),
            'image/png' => imagecreatefrompng($absoluteImagePath),
            'image/webp' => imagecreatefromwebp($absoluteImagePath),
            default => null
        };
        
        if (!$sourceImage) {
            // If can't process, return original
            return base64_encode(file_get_contents($absoluteImagePath));
        }
        
        // Calculate new dimensions (max 1920px width/height for receipts)
        $maxDimension = 1920;
        $scale = min($maxDimension / $originalWidth, $maxDimension / $originalHeight, 1);
        
        if ($scale >= 1) {
            // No need to resize, but compress quality
            ob_start();
            imagejpeg($sourceImage, null, 85); // 85% quality
            $compressedData = ob_get_contents();
            ob_end_clean();
            imagedestroy($sourceImage);
            return base64_encode($compressedData);
        }
        
        $newWidth = (int)($originalWidth * $scale);
        $newHeight = (int)($originalHeight * $scale);
        
        // Create new resized image
        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency for PNG
        if ($mimeType === 'image/png') {
            imagealphablending($resizedImage, false);
            imagesavealpha($resizedImage, true);
            $transparent = imagecolorallocatealpha($resizedImage, 255, 255, 255, 127);
            imagefill($resizedImage, 0, 0, $transparent);
        }
        
        // Resize image
        imagecopyresampled(
            $resizedImage, $sourceImage, 
            0, 0, 0, 0, 
            $newWidth, $newHeight, 
            $originalWidth, $originalHeight
        );
        
        // Output as JPEG with good quality
        ob_start();
        imagejpeg($resizedImage, null, 85);
        $optimizedData = ob_get_contents();
        ob_end_clean();
        
        // Clean up
        imagedestroy($sourceImage);
        imagedestroy($resizedImage);
        
        return base64_encode($optimizedData);
    }
}
