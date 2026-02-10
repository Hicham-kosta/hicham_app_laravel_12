<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommissionHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommissionDashboardController extends Controller
{
    public function index()
    {
        $data = [
            'totalSales' => CommissionHistory::sum('subtotal'),
            'totalCommission' => CommissionHistory::sum('commission_amount'),
            'totalVendorAmount' => CommissionHistory::sum('vendor_amount'),
            'pendingCommission' => CommissionHistory::pending()->sum('commission_amount'),
            'paidCommission' => CommissionHistory::paid()->sum('commission_amount'),
        ];

        $monthlyCommission = CommissionHistory::selectRaw('
                MONTH(commission_date) as month,
                SUM(commission_amount) as total
            ')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $vendorLeaderboard = CommissionHistory::selectRaw('
                vendor_id,
                SUM(commission_amount) as total_commission
            ')
            ->with('vendor')
            ->groupBy('vendor_id')
            ->orderByDesc('total_commission')
            ->limit(5)
            ->get();

        $pendingPayouts = CommissionHistory::pending()
            ->with(['vendor', 'order'])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.commissions.dashboard', compact(
            'data',
            'monthlyCommission',
            'vendorLeaderboard',
            'pendingPayouts'
        ));
    }

    public function markAsPaid(Request $request)
    {
        $request->validate([
            'commission_ids' => 'required|array',
            'payment_method' => 'required',
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->commission_ids as $id) {
                $commission = CommissionHistory::findOrFail($id);

                $commission->markAsPaid(
                    $request->payment_method,
                    $request->payment_reference,
                    $request->payment_notes,
                    auth('admin')->id()
                );
            }
        });

        return back()->with('success', 'Vendor payout processed successfully.');
    }
}
