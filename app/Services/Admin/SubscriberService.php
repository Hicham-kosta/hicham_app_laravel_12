<?php

namespace App\Services\Admin;

use App\Models\Subscriber;
use App\Models\AdminsRole;
use Illuminate\Support\Facades\Auth;


/**
 * Return Subscribers and module permission info
 * 
 * @return array
 */

class SubscriberService
{

    public function subscribers(): array
    {
        $admin = Auth::guard('admin')->user();
        $subscribers = Subscriber::orderBy('created_at', 'desc')->get();
        $subscribersModuleCount = AdminsRole::where([
            'subadmin_id' => $admin->id,
            'module' => 'subscribers'
        ])->count();

        $subscribersModule = [];

        if($admin->role == "admin"){
            $subscribersModule = [
                'view_access' => 1,
                'edit_access' => 1,
                'full_access' => 1,
            ];
        }elseif($subscribersModuleCount == 0){
            return [
                'status' => 'error',
                'message' => 'You do not have permission to view subscribers'
            ];
        }else{
            $subscribersModule = AdminsRole::where([
                'subadmin_id' => $admin->id,
                'module' => 'subscribers'
            ])->first()->toArray();
        }

        return [
            'status' => 'success',
            'subscribers' => $subscribers,
            'subscribersModule' => $subscribersModule
        ];
    }

    /** 
     * Toggle Subscriber Status
     */

    public function updateSubscriberStatus(array $data): int
    {
        $status = ($data['status'] == 'Active') ? 0 : 1;
        Subscriber::where('id', $data['subscriber_id'])->update(['status' => $status]);
        return $status;
    }

    /**
     * Delete a Subscriber
     */
    public function deleteSubscriber($id): array
    {
        $subscriber = Subscriber::findOrFail($id);
        $subscriber->delete();
        return [
            'status' => 'success',
            'message' => 'Subscriber deleted successfully'
        ];
    }
}