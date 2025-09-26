<?php

namespace App\Services\Admin;

use App\Models\Filter;
use App\Models\AdminsRole;
use App\Requests\Admin\FilterRequest;
use Illuminate\Support\Facades\Auth;

class FilterService
{
    public function getAll()
    {
       // 1 Fetch all filters (with relations needed)
       $filters = Filter::with('categories')->get();

       // 2 Set Admin/Subadmin module permissions fo filters
       $filtersModuleCount = AdminsRole::where([
        'subadmin_id' => Auth::guard('admin')->user()->id,
        'module' => 'filters'
       ])->count();

       $status = 'success';
       $message = "";
       $filtersModule = [];
       if(Auth::guard('admin')->user()->role == "admin"){
        // Full access for admin
        $filtersModule = [
            'view_access' => 1,  
            'edit_access' => 1,
            'full_access' => 1
        ];
       }elseif($filtersModuleCount == 0){
        $status = 'error';
        $message = "You don't have access to this module";
       }else{
        // Get permissions for subadmin
        $filtersModule = AdminsRole::where([
            'subadmin_id' => Auth::guard('admin')->user()->id,
            'module' => 'filters'
           ])->first()->toArray();
       }
         return [
          'filters' => $filters,
          'filtersModule' => $filtersModule,
          'status' => $status,
          'message' => $message
         ];
    }

    public function store(array $data)
    {
        return Filter::create([
            'filter_name' => $data['filter_name'],
            'filter_column' => $data['filter_column'],
            'sort' => $data['sort'] ?? 0,
            'status' => $data['status'] ?? 1,
        ]);

        $filter->categories()->sync($data['category_ids']);
        return $filter;
    }

    public function find($id)
    {
        return Filter::with('categories')->findOrFail($id);
    }

    public function update($id, array $data)
    {
        $filter = $this->find($id);
        $filter->update([
            'filter_name' => $data['filter_name'],
            'filter_column' => $data['filter_column'],
            'sort' => $data['sort'] ?? 0,
            'status' => $data['status'] ?? 1,
        ]);

        $filter->categories()->sync($data['category_ids']);
        return $filter;
    }

    public function delete($id)
    {
        $filter = $this->find($id);
        $filter->categories()->detach();
        return $filter->delete();
    }

    public function updateFilterStatus($data)
    {
        $status = ($data['status'] == "Active") ? 0 : 1;
        Filter::where('id', $data['filter_id'])->update(['status' => $status]);
        return $status;
    }
}