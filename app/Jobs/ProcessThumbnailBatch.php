<?php

namespace App\Jobs;

use App\Models\ThumbnailRequest;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessThumbnailBatch implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ThumbnailRequest $thumbnailRequest,
        int $priority = 1
    ) {
        // Use default queue for now
    }

    public function handle(): void
    {
        $this->thumbnailRequest->update(['status' => 'processing']);

        foreach ($this->thumbnailRequest->images as $image) {
            ProcessSingleThumbnail::dispatch($image);
        }
    }
}