<?php

namespace App\Services\Admin;

use App\Models\Review;
use App\Models\AdminsRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReviewService
{
    public function reviews(): array
    {
        $admin = Auth::guard('admin')->user();
        $reviews = Review::orderBy('id', 'desc')->get();
        $reviewsModuleCount = AdminsRole::where(['subadmin_id' => $admin->id, 'module' => 'reviews'])->count();
        if($admin->role === 'admin'){
            $reviewsModule = ['view_access' => 1, 'edit_access' => 1, 'full_access' => 1];
        }elseif($reviewsModuleCount == 0){
            return ['stattus' => 'error', 'message' => 'You do not have access to this module'];
        }else{
            $reviewsModule = AdminsRole::where(['subadmin_id' => $admin->id, 'module' => 'reviews'])->first()->toArray();
        }
        return ['status' => 'success', 'reviews' => $reviews, 'reviewsModule' => $reviewsModule];
    
    }

    public function addEditReview($request): string
    {
        $data = is_array($request) ? $request : $request->
        only(['id', 'product_id', 'rating', 'review', 'status']);
        if(!empty($data['id'])){
            $review = Review::find($data['id']);
            if(!$review) return 'Review not found';
        }else{
            $review = new Review();
        }
        $review->product_id = $data['product_id'] ?? $review->product_id;
        $review->rating = $data['rating'] ?? $review->rating;
        $review->review = $data['review'] ?? $review->review;
        if(isset($data['status']))$review->status = $data['status'];
        $review->save();
        return isset($data['id']) ? 'Review updated successfully' : 'Review added successfully';
    }

    public function updateReviewStatus(array $data)
    {
    Log::info('ReviewService: Updating status', $data);
    
    if (!isset($data['review_id']) || !isset($data['status'])) {
        throw new \Exception('Missing required parameters: review_id and status are required');
    }
    
    $review = Review::find($data['review_id']);
    if (!$review) {
        throw new \Exception('Review not found with ID: ' . $data['review_id']);
    }
    
    // Toggle the status: if current is Active, set to inactive (0), and vice versa
    $newStatus = ($data['status'] === 'Active') ? 0 : 1;
    
    Log::info('Updating review status', [
        'review_id' => $review->id,
        'old_status' => $review->status,
        'new_status' => $newStatus,
        'input_status' => $data['status']
    ]);
    
    $review->status = $newStatus;
    $review->save();
    
    return $newStatus;
   }
    
    public function deleteReview($id): string
    {
        $review = Review::find($id);
        if(!$review) return 'Review not found';
        $review->delete();
        return 'Review deleted successfully';
    }
}