<?php

namespace App\Services;

class NodeJsSimulatorService
{
    public function processThumbnail(string $imageUrl): array
    {
        // Simulate processing delay
        sleep(rand(1, 3));

        // Simulate 90% success rate
        if (rand(1, 10) <= 9) {
            return [
                'success' => true,
                'thumbnail_url' => 'thumbnails/' . md5($imageUrl) . '_thumb.jpg',
            ];
        }

        return [
            'success' => false,
            'error' => 'Failed to process image: Invalid format or network error',
        ];
    }
}