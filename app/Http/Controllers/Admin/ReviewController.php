<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\ReviewRequest;
use App\Services\Admin\ReviewService;
use App\Models\Review;
use App\Models\ColumnPreference;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class ReviewController extends Controller
{
    protected $reviewService;
    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Session::put('page', 'reviews');
        $result = $this->reviewService->reviews();
        if($result['status'] === 'error'){
            return redirect('admin/dashboard')->with('error_message', $result['message']);
        }
        $reviews = $result['reviews'];
        $reviewsModule = $result['reviewsModule'];
        $columnPrefs = ColumnPreference::where('admin_id', Auth::guard('admin')->id())
        ->where('table_name', 'reviews')
        ->first();
        $reviewsSavedOrder = $columnPrefs ? json_decode($columnPrefs->column_order, true) : null;
        $reviewsHiddenCols = $columnPrefs ? json_decode($columnPrefs->hidden_columns, true) : [];

        return view('admin.reviews.index')->with(compact(
            'reviews',
            'reviewsModule',
            'reviewsSavedOrder',
            'reviewsHiddenCols'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Add Review';
        $review = new Review();
        return view('admin.reviews.add_eddit_review', compact('title', 'review'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ReviewRequest $request)
    {
        $message = $this->reviewService->addEditReview($request);
        return redirect()->route('reviews.index')->with('success_message', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $title = 'Edit Review';
        $review = Review::findOrFail($id);
        return view('admin.reviews.add_edit_review', compact('title', 'review'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->merge(['id' => $id]);
        $message = $this->reviewService->addEditReview($request);
        return redirect()->route('reviews.index')->with('success_message', $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $result = $this->reviewService->deleteReview($id);
        return redirect()->back()->with('success_message', $result);
    }

    public function updateReviewStatus(Request $request)
    {
    // Log the incoming request for debugging
    Log::info('Update Review Status Request Received', [
        'all_data' => $request->all(),
        'ajax' => $request->ajax(),
        'wants_json' => $request->wantsJson(),
        'headers' => $request->headers->all()
    ]);
    try {
        $data = $request->all();
        
        // Validate required fields
        $validator = Validator::make($data, [
            'status' => 'required|in:Active,Inactive',
            'review_id' => 'required|exists:reviews,id'
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed', ['errors' => $validator->errors()]);
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $status = $this->reviewService->updateReviewStatus($data);
        
        Log::info('Review status updated successfully', [
            'review_id' => $data['review_id'],
            'new_status' => $status
        ]);

        return response()->json([
            'status' => $status,
            'review_id' => $data['review_id'],
            'message' => 'Status updated successfully'
        ]);

    } catch (\Exception $e) {
        Log::error('Error in updateReviewStatus: ' . $e->getMessage(), [
            'exception' => $e,
            'request_data' => $request->all()
        ]);

        return response()->json([
            'error' => 'Server error',
            'message' => $e->getMessage()
        ], 500);
    }
    }
}
