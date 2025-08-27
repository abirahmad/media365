<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ThumbnailRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThumbnailController extends Controller
{
    public function status(Request $request)
    {
        $query = Auth::user()->thumbnailRequests()->with('images');

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $requests = $query->latest()->paginate(10);

        return response()->json([
            'requests' => $requests,
            'timestamp' => now()->toISOString(),
        ]);
    }

    public function requestStatus($id)
    {
        $request = ThumbnailRequest::with('images')->findOrFail($id);
        
        if ($request->user_id !== Auth::id()) {
            abort(403);
        }

        return response()->json([
            'request' => $request,
            'timestamp' => now()->toISOString(),
        ]);
    }
}