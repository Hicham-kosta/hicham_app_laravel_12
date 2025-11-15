<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\WalletCreditRequest;
use App\Services\Admin\WalletCreditService;
use App\Models\WalletCredit;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class WalletCreditController extends Controller
{
    public function __construct(private WalletCreditService $walletCreditService){}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Session::put('page', 'wallet-credits');
        $result = $this->walletCreditService->walletCredits();
        if($result['status'] === 'error'){
            return redirect('admin/dashboard')->with('error_message', $result['message']);
        }

        $userIds = $result['credits']->pluck('user_id')->unique()->filter()->values()->all();
        $balanceMap = $this->walletCreditService->activeBalanceMap($userIds);

        // build running balance per row (consider only active credits & non-expired-entires)
        $runningMap = [];
        $running = [];

        // Work on ASC order to accumulate credits
        $asc = $result['credits']->sortBy('created_at');

        foreach($asc as $row){
            $uid = (int) $row->user_id;

            //count only active + non-expired rows in the running balance
            $eligible = ($row->is_active == 1) && (is_null($row->expires_at) || $row->expires_at->gte(now()));

            if(!isset($running[$uid])) $running[$uid] = 0.0;
            if($eligible){
                $running[$uid] += (float) $row->amount;
            }
            // record running balance for this row id
            $runningMap[$row->id] = $running[$uid];
        }

            return view('admin.wallet_credits.index', [
                'credits' => $result['credits'],
                'walletCreditsModule' => $result['walletCreditsModule'],
                'runningMap' => $runningMap,
            ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Add Wallet Credit/Debit';
        $users = $this->walletCreditService->usersForSelect();
        $balanceMap = $this->walletCreditService->activeBalanceMap($users->pluck('id')->all());
        return view('admin.wallet_credits.add_edit_wallet_credit', compact('title', 'users', 'balanceMap'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(WalletCreditRequest $request)
    {
    $result = $this->walletCreditService->addEditWalletCredit($request);
    return redirect()->route('wallet-credits.index')->with('success_message', $result['message']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $title = 'Edit Wallet Credit/Debit';
        $entry = WalletCredit::findOrFail($id);
        $users = $this->walletCreditService->usersForSelect();
        $balanceMap = $this->walletCreditService->activeBalanceMap($users->pluck('id')->all());

        return view('admin.wallet_credits.add_edit_wallet_credit', compact('title', 'entry', 'users', 'balanceMap'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(WalletCreditRequest $request, string $id)
    {
    $request->merge(['id' => $id]);
    $result = $this->walletCreditService->addEditWalletCredit($request);
    return redirect()->route('wallet-credits.index')->with('success_message', $result['message']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $result = $this->walletCreditService->deleteWalletCredit($id);
        return redirect()->back()->with('success_message', $result['message']);
    }

    public function updateWalletCreditStatus(Request $request)
{
    if($request->ajax()){
        $data = $request->all();
        
        // Debug the incoming data
        Log::info('Update Wallet Status Request:', $data);
        
        $status = $this->walletCreditService->updateWalletCreditStatus($data);
        
        // Return proper JSON response
        return response()->json([
            'status' => $status,
            'wallet_credit_id' => $data['wallet_credit_id'],
            'message' => 'Status updated successfully'
        ]);
    }
    
    return response()->json(['error' => 'Invalid request'], 400);
}
}
