<?php

namespace App\Services\Admin;

use App\Models\FilterValue;
use App\Models\AdminsRole;
use Illuminate\Support\Facades\Auth;

class FilterValueService
{
    public function getAll($filterId)
    {
        $filtersValues = FilterValue::where('filter_id', $filterId)->get();
        $filtersValuesModuleCount = AdminsRole::where([
            'subadmin_id' => Auth::guard('admin')->user()->id,
            'module' => 'filters_values'
        ])->count();
        $status = "success";
        $message = "";
        $filtersValuesModule = [];
        if (Auth::guard('admin')->user()->role == "admin") {
            $filtersValuesModule = [
                'view_access' => 1,
                'edit_access' => 1,
                'full_access' => 1
            ];
        }elseif($filtersValuesModuleCount == 0){
            $status = "error";
            $message = "You don't have access to this module";
        } else {
            $filtersValuesModule = AdminsRole::where([
                'subadmin_id' => Auth::guard('admin')->user()->id,
                'module' => 'filters_values'
            ])->first()->toArray();
        }
        return [
            'filterValues' => $filtersValues,
            'filterValuesModule' => $filtersValuesModule,
            'status' => $status,
            'message' => $message,
            
            
        ];
    }

    public function store(array $data, $filterId)
    {
        return FilterValue::create([
            'filter_id' => $filterId,
            'value' => $data['value'],
            'sort' => $data['sort'] ?? 0,
            'status' => $data['status'] ?? 1,
        ]);
    }

    public function find($filterId, $id)
    {
        return FilterValue::where('filter_id', $filterId)->findOrFail($id);
    }

    public function update(array $data, $filterId, $id)
    {
        $filterValue = $this->find($filterId, $id);
        $filterValue->update([
            'value' => $data['value'],
            'sort' => $data['sort'] ?? 0,
            'status' => $data['status'] ?? 1,
        ]);
        return $filterValue;
    }

    public function delete($filterId, $id)
    {
        $filterValue = $this->find($filterId, $id);
        return $filterValue->delete();
    }
}