<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ThumbnailRequest extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'total_images',
        'processed_images',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ThumbnailImage::class);
    }

    public function isCompleted(): bool
    {
        return $this->processed_images >= $this->total_images;
    }

    public function incrementProcessed(): void
    {
        $this->increment('processed_images');
        
        if ($this->isCompleted()) {
            $this->update(['status' => 'completed']);
        }
    }
}