<?php

namespace App\Policies;

use App\Models\ThumbnailRequest;
use App\Models\User;

class ThumbnailRequestPolicy
{
    public function view(User $user, ThumbnailRequest $thumbnailRequest): bool
    {
        return $user->id === $thumbnailRequest->user_id;
    }
}