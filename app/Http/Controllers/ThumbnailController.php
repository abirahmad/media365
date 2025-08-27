<?php

namespace App\Http\Controllers;

use App\Models\ThumbnailRequest;
use App\Services\ThumbnailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Inertia\Inertia;

class ThumbnailController extends Controller
{
    use AuthorizesRequests;

    private ThumbnailService $thumbnailService;

    public function __construct(ThumbnailService $thumbnailService)
    {
        $this->thumbnailService = $thumbnailService;
    }

    public function index(Request $request)
    {
        $query = Auth::user()->thumbnailRequests()->with('images');

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $requests = $query->latest()->paginate(10);

        return Inertia::render('Thumbnails/Index', [
            'requests' => $requests,
            'filters' => $request->only('status'),
            'userTier' => Auth::user()->tier,
            'quotaLimit' => Auth::user()->getQuotaLimit(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'image_urls' => 'required|string',
        ]);

        $urls = array_filter(
            array_map('trim', explode("\n", $request->image_urls)),
            fn($url) => filter_var($url, FILTER_VALIDATE_URL)
        );

        if (empty($urls)) {
            return back()->withErrors(['image_urls' => 'Please provide valid image URLs']);
        }

        try {
            $thumbnailRequest = $this->thumbnailService->createThumbnailRequest(
                Auth::user(),
                $urls
            );

            return redirect()->route('thumbnails.index')
                ->with('success', "Processing {$thumbnailRequest->total_images} images");
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['image_urls' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $thumbnailRequest = ThumbnailRequest::with('images')->findOrFail($id);

        return Inertia::render('Thumbnails/Show', [
            'request' => $thumbnailRequest,
        ]);
    }
}