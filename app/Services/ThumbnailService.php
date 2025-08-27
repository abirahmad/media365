<?php

namespace App\Services;

use App\Models\User;
use App\Models\ThumbnailRequest;
use App\Jobs\ProcessThumbnailBatch;
use Illuminate\Support\Facades\DB;

class ThumbnailService
{
    public function createThumbnailRequest(User $user, array $imageUrls): ThumbnailRequest
    {
        $quota = $user->getQuotaLimit();
        $imageCount = count($imageUrls);

        if ($imageCount > $quota) {
            throw new \InvalidArgumentException("Image count ({$imageCount}) exceeds quota limit ({$quota})");
        }

        return DB::transaction(function () use ($user, $imageUrls, $imageCount) {
            $request = ThumbnailRequest::create([
                'user_id' => $user->id,
                'total_images' => $imageCount,
                'status' => 'pending',
            ]);

            foreach ($imageUrls as $url) {
                $request->images()->create([
                    'original_url' => trim($url),
                    'status' => 'pending',
                ]);
            }

            ProcessThumbnailBatch::dispatch($request, $user->getPriority());

            return $request;
        });
    }
}