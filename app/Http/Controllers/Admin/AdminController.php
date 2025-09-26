<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ColumnPreference;
use App\Http\Requests\Admin\LoginRequest;
use App\Http\Requests\Admin\PasswordRequest;
use App\Http\Requests\Admin\DetailRequest;
use App\Http\Requests\Admin\SubadminRequest;
use App\Services\Admin\AdminService;
use App\Models\AdminsRole;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    protected $adminService;
    // Injecting the AdminService into the controller

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Session::put('page', 'dashboard');
        $categoriesCount = \App\Models\Category::get()->count();
        $productsCount = \App\Models\Product::get()->count();
        $brandsCount = \App\Models\Brand::get()->count();
        $usersCount = \App\Models\User::get()->count();
        $couponsCount = 0;
        $ordersCount = 0;
        $pagesCount = 0;
        $enquiriesCount = 0;
        return view('admin.dashboard')->with(Compact(
            'categoriesCount', 
            'productsCount', 
            'brandsCount', 
            'usersCount', 
            'couponsCount', 
            'ordersCount', 
            'pagesCount', 
            'enquiriesCount'
        ));       
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.login');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LoginRequest $request)
    {
        $data = $request->all();

        $loginStatus = $this->adminService->login($data);

        if($loginStatus = "success") {
            return redirect()->route('dashboard.index');
        }elseif($loginStatus = "inactive") {
            return redirect()->back()->with('error_message', 'Your account is inactive, Please 
            contact the Administrator!');
        }else {
            return redirect()->back()->with('error_message', 'Invalid email or password!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Admin $admin)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Admin $admin)
    {
        Session::put('page', 'update-password');
        return view('admin.update_password');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Admin $admin)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admin $admin)
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }

    // Verify Password
    public function verifyPassword(Request $request) {
        $data = $request->all();
        return $this->adminService->verifyPassword($data);
    }

    // Update Password
    public function updatePasswordRequest(PasswordRequest $request) {
        if($request->isMethod('post')) {
            $data = $request->input();
            $pwdStatus = $this->adminService->updatePassword($data);
            if($pwdStatus['status'] == 'success') {
                return redirect()->back()->with('success_message', $pwdStatus['message']);
            } else {
                return redirect()->back()->with('error_message', $pwdStatus['message']);
            }  
        }
    }

    public function editDetails() {
        Session::put('page', 'update-details');
        return view('admin.update_details');
    }

    public function updateDetails(DetailRequest $request) {
        Session::put('page', 'update-details');
        if($request->isMethod('post')) {
            $this->adminService->updateDetails($request);
            return redirect()->back()->with('success_message', 'Details updated successfully!');
        }

    }

    public function deleteProfileImage(Request $request) {
        $status = $this->adminService->deleteProfileImage($request->admin_id);
        return response()->json($status);
    }
    public function subadmins() {
        Session::put('page', 'subadmins');
        $subadmins = $this->adminService->subadmins();
        return view('admin.subadmins.subadmins')->with(compact('subadmins'));
   }

   public function updateSubadminStatus(Request $request){
      if($request->ajax()) {
        $data = $request->all();
        $status = $this->adminService->updateSubadminStatus($data);
        return response()->json(['status' => $status, 'subadmin_id' => $data['subadmin_id']]); 
      } 
   }

   public function deleteSubadmin($id){
    $result = $this->adminService->deleteSubadmin($id);
    return redirect()->back()->with('success_message', $result['message']);
   }

   public function addEditSubadmin($id = null){
    if($id == ""){
        $title = "Add Subadmin";
        $subadmindata = array();
    }else{
        $title = "Edit Subadmin";
        $subadmindata = Admin::find($id);
    }
    return view('admin.subadmins.add_edit_subadmin')->with(compact('title', 'subadmindata'));
   }

   public function addEditSubadminRequest(SubadminRequest $request){
    if($request->isMethod('post')){
        $result = $this->adminService->addEditSubadmin($request);
        return redirect('admin/subadmins')->with('success_message', $result['message']);
    }

   }
   
   public function updateRole($id){
    $subadminRoles = AdminsRole::where('subadmin_id', $id)->get()->toArray();
    $subadminDetails = Admin::where('id', $id)->first()->toArray();
    $modules = ['categories', 'products', 'orders', 'users', 'subscribers', 'filters', 'filters_values'];
    $title = "Update ".$subadminDetails['name']." Subadmin Roles/Permissions";
    return view('admin.subadmins.update_roles')->with(compact('title', 'id', 'subadminRoles', 'modules'));
   }

   public function updateRoleRequest(Request $request){
    if($request->isMethod('post')){
        $data = $request->all();
        $service = new AdminService();
        $result = $service->updateRole($request);
        return redirect()->back()->with('success_message', $result['message']);
    }

   }

   public function saveColumnOrder(Request $request) {

        $userId = Auth::guard('admin')->id();
        $tableName = $request->table_key;
        if(!$tableName){
            return response()->json(['status' => 'error', 'message' => 'Table key is required!']);
        }
        ColumnPreference::updateOrCreate(
            ['admin_id' => $userId, 'table_name' => $tableName],
            ['column_order' => json_encode($request->column_order)]
        );
        return response()->json(['status' => 'success']);
    }

    public function saveColumnVisibility(Request $request) {
        $userId = Auth::guard('admin')->id();
        $tableName = $request->table_key;
        if(!$tableName){
            return response()->json(['status' => 'error', 'message' => 'Table key is required!']);
        }
        ColumnPreference::updateOrCreate(
            ['admin_id' => $userId, 'table_name' => $tableName],
            [
                'column_order' => json_encode($request->column_order),
                'hidden_column' => json_encode($request->hidden_column)
            ]
        );
        return response()->json(['status' => 'success']);
    }


}