<?php

namespace App\Services\Front;

use App\Models\Review;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReviewService
{
    public function addReview(array $data): array
    {
        $userId = $data['user_id'] ?? Auth::id();
        $productId = $data['product_id'] ?? null;
        $rating = $data['rating'] ?? null;
        $reviewText = $data['review'] ?? null;
        if(!$userId || !$productId || !$rating) {
            return ['status' => 'error', 'message' => 'Invalid data provided'];
        }
        if(Review::where('user_id', $userId)->where('product_id', $productId)->exists()) {
            return ['status' => 'error', 'message' => 'You have already reviewed this product'];
        }
        try {
           Review::create([
            'product_id' => $productId,
            'user_id' => $userId,
            'rating' => (int)$rating,
            'review' => $reviewText,
            'status' => 0, // Pending
           ]);
        }catch(QueryException $e) {
            Log::error('Rview create failed: ' . $e->getMessage());
            return ['status' => 'error', 'message' => 'Review create failed'];
        }
        return ['status' => 'success', 'message' => 'Thank you! Your review has been submitted for approval'];
    }
}