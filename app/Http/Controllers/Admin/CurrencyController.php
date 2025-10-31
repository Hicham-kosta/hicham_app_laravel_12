<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CurrencyRequest;
use App\Models\Currency;
use App\Models\ColumnPreference;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Services\Admin\CurrencyService;

class CurrencyController extends Controller
{
    protected $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    /**
     * Display a listing of the currencies.
     */
    public function index()
    {
        Session::put('page', 'currencies');

        $result = $this->currencyService->currencies();
        if ($result['status'] === 'error') {
            return redirect('admin/dashboard')->with('error_message', $result['message']);
        }

        $currencies = $result['currencies'];
        $currenciesModule = $result['currenciesModule'];

        $columnPrefs = ColumnPreference::where('admin_id', Auth::guard('admin')->id())
            ->where('table_name', 'currencies')->first();

        $currenciesSavedOrder = $columnPrefs ? json_decode($columnPrefs->column_order, true) : null;
        $currenciesHiddenCols = $columnPrefs ? json_decode($columnPrefs->hidden_columns, true) : [];

        return view('admin.currencies.index', compact('currencies', 'currenciesModule', 'currenciesSavedOrder', 'currenciesHiddenCols'));
    }

    /**
     * Show the form for creating a new currency.
     */
    

    public function create()
    {
        $currency = new Currency(); // empty model instance
        return view('admin.currencies.add_edit_currency', compact('currency'));
    }

    

    /**
     * Store a newly created currency in storage.
     */
    public function store(CurrencyRequest $request)
    {
        $message = $this->currencyService->addEditCurrency($request);
        return redirect()->route('currencies.index')->with('success_message', $message);
    }

    /**
     * Show the form for editing the specified currency.
     */
    public function edit($id)
    {
        $currency = Currency::findOrFail($id);
        return view('admin.currencies.add_edit_currency', compact('currency'));
    }

    /**
     * Update the specified currency in storage.
     */
        public function update(CurrencyRequest $request, $id)
    {
        $request->merge(['id' => $id]);
        $message = $this->currencyService->addEditCurrency($request);
        
        // FIX: Use correct route name - remove 'admin.' prefix if not defined
        return redirect()->route('currencies.index')->with('success_message', $message);
    }

    /**
     * Remove the specified currency from storage.
     */
    public function destroy($id)
    {
        $result = $this->currencyService->deleteCurrency($id);

        if (!empty($result['status']) && $result['status'] === true) {
            return redirect()->route('currencies.index')->with('success_message', $result['message']);
        }

        return redirect()->route('admin.currencies.index')->with('error_message', $result['message']);
    }

    /**
     * Update the currency status (AJAX request).
     */
        public function updateCurrencyStatus(Request $request)
    {
        if ($request->ajax()) {
            $data = $request->all();

            try {
                $newStatus = $this->currencyService->updateCurrencyStatus($data);
                return response()->json([
                    'status' => 'success',
                    'currency_id' => $data['currency_id'] ?? $data['id'],
                    'status_value' => (int) $newStatus
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error', 
                    'message' => $e->getMessage()
                ], 422);
            }
        }

        return response()->json(['status' => 'error', 'message' => 'Invalid request'], 400);
    }
}

