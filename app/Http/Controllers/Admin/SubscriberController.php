<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\SubscriberService;
use App\Models\Subscriber;
use App\Models\ColumnPreference;
use App\Models\AdminsRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SubscriberController extends Controller
{
    protected $subscriberService;
    public function __construct(SubscriberService $subscriberService)
    {
        $this->subscriberService = $subscriberService;
    }

    /**
     * Display a listing of subscribers
     * 
     */
    public function index()
    {
        Session::put('page', 'subscribers');
        $result = $this->subscriberService->subscribers();
        if($result['status'] === 'error') {
            return redirect('admin/dashboard')->with('error_message', $result['message']);
        }

        $subscribers = $result['subscribers'];
        $subscribersModule = $result['subscribersModule'];
        $columnPrefs = ColumnPreference::where('admin_id', Auth::guard('admin')->id())
        ->where('table_name', 'subscribers')
        ->first();
        $subscribersSavedOrder = $columnPrefs ? json_decode($columnPrefs->column_order, true) : null;
        $subscribersHiddenCols = $columnPrefs ? json_decode($columnPrefs->hidden_columns, true) : [];

        return view('admin.subscribers.index', compact(
            'subscribers', 
            'subscribersModule', 
            'subscribersSavedOrder', 
            'subscribersHiddenCols'
        ));
    }

    public function destroy(string $id)
    {
        $result = $this->subscriberService->deleteSubscriber($id);
        return redirect()->back()->with('success_message', $result['message']);
    }

    public function updateSubscriberStatus(Request $request)
    {
        if($request->ajax()) {
            $data = $request->all();
            $status = $this->subscriberService->updateSubscriberStatus($data);
            return response()->json(['status' => $status, 'subscriber_id' => $data['subscriber_id']]);
        }
    }
}