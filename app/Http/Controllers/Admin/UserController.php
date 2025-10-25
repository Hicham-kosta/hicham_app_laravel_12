<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Services\Admin\UserService;
use App\Http\Requests\Admin\UserFilterRequest;
use Illuminate\Http\Request;
use App\Models\ColumnPreference;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(UserFilterRequest $request)
    {
        Session::put('page', 'users');
        $result = $this->userService->users($request->validated());
        if(isset($result['status']) && $result['status'] === 'error'){
            return redirect('admin/dashboard')->with('error_message', $result['message']);
      }

      $users = $result['users'];
      $usersModule = $result['usersModule'] ?? [];
      $columnPrefs = ColumnPreference::where('admin_id', Auth::guard('admin')->id())
      ->where('table_name', 'users')
      ->first();
      $usersSavedOrder = $columnPrefs ? json_decode($columnPrefs->column_order, true) : null;
      $usersHiddenCols = $columnPrefs ? json_decode($columnPrefs->hidden_columns, true) : [];
      return view('admin.users.index')->with(compact('users', 'usersModule', 'usersSavedOrder', 'usersHiddenCols'));
    }

    public function updateUserStatus(Request $request)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);
        if($request->ajax()){
            $data = $request->all();
            $status = $this->userService->updateUserStatus($data);
            return response()->json(['status' => $status, 'user_id' => $data['user_id']]);
        }
        abort(400);
    }
  }

