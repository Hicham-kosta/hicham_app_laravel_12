<?php

namespace App\Services\Admin;

use App\Models\AdminsRole;
use App\Models\WalletCredit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WalletCreditService
{
    public function walletCredits()
    {
        $credits = WalletCredit::with('user')->orderBy('id', 'desc')->get();

        $admin = Auth::guard('admin')->user();
        $status = 'success';
        $message = "";
        $walletCreditsModule = [];
        if($admin->role = "admin"){
            $walletCreditsModule = [
            'wiew_access' => 1,
            'edit_access' => 1,
            'full_access' => 1,
          ];
       }else{
           $count = AdminsRole::where([
            'subadmin_id'=>  $admin->id,
            'module' => 'wallet_credits',
            ])->count();

            if($count == 0){
                $status = 'error';
                $message = "You don't have access to this module";
            }else{
                $walletCreditsModule = AdminsRole::where([
                    'subadmin_id'=>  $admin->id,
                    'module' => 'wallet_credits',
                    ])->first()->toArray();
            }
        }

        return [
            'status' => $status,
            'message' => $message,
            'credits' => $credits,
            'walletCreditsModule' => $walletCreditsModule,
        ];
    }

    public function usersForSelect($limit = 300)
    {
        return User::orderBy('id', 'desc')->limit($limit)->get(['id', 'name', 'email']);
    }

    public function addEditWalletCredit($request)
    {
        $data = $request->all();

        // build signed amount from action + amount_abs if provided by the form
        if($request->filled('action') && $request->filled('amount_abs')){
            $data['amount'] = $request->input('action') === 'debit' 
            ? -abs((float) $request->input('amount_abs')) 
            : abs((float) $request->input('amount_abs'));
        }

        // default expery + 1 year (endOfDay) if not provided
        $expiresAt = !empty($data['expires_at']) 
        ? Carbon::parse($data['expires_at'])->endOfDay() 
        : Carbon::now()->addYear()->endOfDay();

        if(!empty($data['id'])){
            $credit = WalletCredit::findOrFail($data['id']);
            $message = 'Wallet entry updated successfully';
        }else{
            $credit = new WalletCredit();
            $message = 'Wallet entry added successfully';
        }

        $credit->user_id = (int)$data['user_id'];
        $credit->amount = (float)$data['amount']; // +credit or -debit
        $credit->expires_at = $expiresAt;
        $credit->reason = $data['reason'] ?? null;
        $credit->is_active = !empty($data['is_active']) ? 1 : 0;
        $credit->added_by = Auth::guard('admin')->id();

        $credit->save();

        return [
            'status' => 'success',
            'message' => $message,
        ];
    }

    public function deleteWalletCredit($id)
    {
        WalletCredit::where('id', $id)->delete();

        return [
            'status' => 'success',
            'message' => 'Wallet entry deleted successfully',
        ];
    }

    public function updateWalletCreditStatus($data)
{
    $status = ($data['status'] == 'Active') ? 0 : 1; // Toggle the status
    
    Log::info('Updating wallet credit status', [
        'wallet_credit_id' => $data['wallet_credit_id'],
        'from_status' => $data['status'],
        'to_status' => $status
    ]);
    
    $updated = WalletCredit::where('id', $data['wallet_credit_id'])->update(['is_active' => $status]);
    
    if ($updated) {
        Log::info('Wallet credit status updated successfully');
    } else {
        Log::error('Failed to update wallet credit status');
    }
    
    return $status;
}

    public function activeBalanceMap(array $userIds): array
    {
        if(empty($userIds)) return [];

        return WalletCredit::query()
        ->selectRaw('user_id, SUM(amount) as total')
        ->whereIn('user_id', $userIds)
        ->where('is_active', 1)
        ->where(function($q){
            $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
        })
        ->groupBy('user_id')
        ->pluck('total', 'user_id')
        ->toArray();
    }
}