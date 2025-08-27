<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThumbnailImage extends Model
{
    protected $fillable = [
        'thumbnail_request_id',
        'original_url',
        'thumbnail_url',
        'status',
        'error_message',
    ];

    public function thumbnailRequest(): BelongsTo
    {
        return $this->belongsTo(ThumbnailRequest::class);
    }

    public function markAsProcessed(string $thumbnailUrl): void
    {
        $this->update([
            'status' => 'processed',
            'thumbnail_url' => $thumbnailUrl,
        ]);
    }

    public function markAsFailed(string $errorMessage): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
        ]);
    }
}