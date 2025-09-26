<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FilterValueRequest;
use App\Services\Admin\FilterValueService;
use App\Models\Filter;
use App\Models\ColumnPreference;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class FilterValueController extends Controller
{
    protected $filterValueService;

    public function __construct(FilterValueService $filterValueService)
    {
        $this->filterValueService = $filterValueService;
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index($filterId)
    {
        Session::put('page', 'filters_values');
        // Load the parent filter for the heading (with 404 if not found)
        $filter = Filter::findOrFail($filterId);
        // Get all filter values for this filter
        $result = $this->filterValueService->getAll($filterId);

        if($result['status'] == 'error'){
            return redirect('admin/dashboard')->with('error_message', 'You don\'t have access to this module');
        }

        // IMPOTANT use the exact variable name the Blade expects (filterValues)
        $filterValues = $result['filterValues'];
        $filterValuesModule = $result['filterValuesModule'];
        $columnPrefs = ColumnPreference::where('admin_id', Auth::guard('admin')->id())
            ->where('table_name', 'filters_values')
            ->first();
        $filterValuesSavedOrder = $columnPrefs ? json_decode($columnPrefs->column_order, true) : null;
        $filterValuesHiddenCols = $columnPrefs ? json_decode($columnPrefs->hidden_columns, true) : [];


        return view('admin.filter_values.index', compact(
            'filter', 
            'filterValues',
            'filterValuesModule',
            'filterValuesSavedOrder',
            'filterValuesHiddenCols'
        ))->with('title', 'Filter Values');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($filterId)
    {
        $filter = Filter::findOrFail($filterId);
        $title = "Add Filter Value";
        return view('admin.filter_values.add_edit_filter_value', compact('filter', 'title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FilterValueRequest $request, $filterId)
    {
        $filter = Filter::findOrFail($filterId);
        $this->filterValueService->store($request->validated(), $filterId);
        return redirect()->route('filter-values.index', $filter->id)
           ->with('success_message', 'Filter value added successfully.');
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
    public function edit($filterId, $id)
    {
        $filter = Filter::findOrFail($filterId);
        $filterValue = $this->filterValueService->find($filterId, $id);
        $title = "Edit Filter Value";
        return view('admin.filter_values.add_edit_filter_value', compact('filter', 'filterValue', 'title'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FilterValueRequest $request, $filterId, $id)
    {
        $filter = Filter::findOrFail($filterId);
        $this->filterValueService->update($request->validated(), $filterId, $id);
        return redirect()->route('filter-values.index', $filter->id)
            ->with('success_message', 'Filter value updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($filterId, $id)
    {
        $this->filterValueService->delete($filterId, $id);
        return redirect()->route('filter-values.index', $filterId)
            ->with('success_message', 'Filter value deleted successfully.');
    }
}
