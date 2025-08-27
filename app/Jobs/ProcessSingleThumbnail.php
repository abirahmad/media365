<?php

namespace App\Jobs;

use App\Models\ThumbnailImage;
use App\Services\NodeJsSimulatorService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessSingleThumbnail implements ShouldQueue
{
    use Queueable;

    public function __construct(public ThumbnailImage $thumbnailImage)
    {
        $this->onQueue('default');
    }

    public function handle(NodeJsSimulatorService $nodeService): void
    {
        $result = $nodeService->processThumbnail($this->thumbnailImage->original_url);

        if ($result['success']) {
            $this->thumbnailImage->markAsProcessed($result['thumbnail_url']);
        } else {
            $this->thumbnailImage->markAsFailed($result['error']);
        }

        $this->thumbnailImage->thumbnailRequest->incrementProcessed();
    }
}