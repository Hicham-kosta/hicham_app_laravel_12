<?php

namespace App\Services\Front;


use App\Models\Page;
use Illuminate\Support\Facades\Cache;

class PageService
{
    /**
     * Get page by slug(url)
     * 
     * @param string $url
     * @return array
     */
    public function getPageByUrl($url)
    {
        // Cache key
        $cacheKey = "page_{$url}";

        //Cache for 60 minutes
        $page = Cache::remember($cacheKey, 60*60, function () use ($url) {
            return Page::where('url', $url)
            ->where('status', 1)  // Only active pages
            ->first();
        });

        if (!$page) {
            return [
                'status' => 'error',
                'message' => 'Page not found',
            ];
        }

        return [
            'status' => 'success',
            'page' => $page,
        ];
    }

    /**
     * Optional: Helper to invalidate cache when admin admin edits pages
     * Call this from admin Page Service after save/edit/update
     * 
     */
    public static function clearCacheFor(string $url): void
    {
        Cache::forget("page_{$url}");
    }
}   