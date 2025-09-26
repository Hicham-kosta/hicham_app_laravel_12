<?php


namespace App\Services\Admin;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\Admin;
use Illuminate\Http\Request;
use App\Models\AdminsRole;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class AdminService
{
    public function login($data)
    {
        $admin = Admin::where('email', $data['email'])->first();
        if($admin){
            if($admin->status == 0){
                return "inactive";
            }

        if(Auth::guard('admin')->attempt(['email' => $data['email'], 'password' => $data[
            'password'], 'status' => 1 ])) {
            // Remember Admin Email and Password
            if(!empty($data['remember'])) {
                setcookie("email", $data["email"], time()+ 3600); // 1 hour
                setcookie("password", $data["password"], time()+ 3600); // 1 hour           
            } else {
                setcookie("email", "");
                setcookie("password", "");
            }
            return "Success"; // Return Success if login is successful  
        } else {
            return "Invalid"; //Return Invalid if Credentials are Incorrect
        }
        } else{
            return "Invalid"; // Return Invalid if Email is not found 
    }
}

    public function verifyPassword($data) {
        if(Hash::check($data['current_pwd'], Auth::guard('admin')->user()->password)) {
            return "true";
        } else {
            return "false";
        }
    }

    public function updatePassword($data) {
        
        // Check if the current password is correct
        if(Hash::check($data['current_pwd'], Auth::guard('admin')->user()->password)){
            // Check if the new password and confirm password match
            if($data['new_pwd'] == $data['confirm_pwd']) {
                Admin::where('email', Auth::guard('admin')->user()->email)->update(['password' => bcrypt($data['new_pwd'])]);
                $status = "success";
                $mesaage = "Password updated successfully!";
            } else {
                $status = "error";
                $mesaage = "New password and confirm password do not match!";
            }
        }
        else {
            $status = "error";
            $mesaage = "Current password is incorrect!";
        }
        return["status" => $status, 'message' => $mesaage];
    }

    public function updateDetails($request) {
        $data = $request->all();

         // upload image
         if($request->hasFile('image')) {
            $image_tmp = $request->file('image');
            if($image_tmp->isValid()) {
                $manager = new ImageManager(new Driver()); // Create a new ImageManager instance
                $image = $manager->read($image_tmp);
                $extension = $image_tmp->getClientOriginalExtension();
                $imageName = rand(111, 99999).'.'.$extension;
                $image_path = public_path('admin/images/profiles/'.$imageName); // Path to save the image
                $image->save($image_path);
            }
        } else if(!empty($data['current_image'])) {
            $imageName = $data['current_image'];
        } else {
            $imageName = "";
        }

        // Update Admin details
        Admin::where('email', Auth::guard('admin')->user()->email)->update( [
            'name' => $data['name'], 
            'mobile' => $data['mobile'],
            'image' => $imageName
        ]);
    }

    public function deleteProfileImage($adminId) {
        $profileImage = Admin::where('id', $adminId)->value('image');
        if($profileImage){
            $profile_image_path = 'admin/images/profiles/'.$profileImage;
            if(file_exists(public_path($profile_image_path))) {
                unlink(public_path($profile_image_path)); // Delete the image from the server
            }
            Admin::where('id', $adminId)->update(['image' => null]); // Update the database to remove the image path
            return ['status' => true, 'message' => 'Profile image deleted successfully!'];
        }
            return ['status' => false, 'message' => 'Profile image not found!'];
    }

    public function subadmins() {
        $subadmins = Admin::where('role', 'subadmin')->get();
        return $subadmins;
    }

    public function updateSubadminStatus($data){
        $status = ($data['status'] == "Active") ? 0 : 1;
        Admin::where('id', $data['subadmin_id'])->update(['status' => $status]);
        return $status;
    }

    public function deleteSubadmin($id){
        Admin::where('id', $id)->delete();
        $message = "Subadmin deleted succefully!";
        return array('message' => $message);
    }

    public function addEditSubadmin($request){
        $data = $request->all();

        if(isset($data['id']) && $data['id']!= ""){
            $subadmindata = Admin::find($data['id']);
            $message = "Subadmin updated succefully!";
        }else{
            $subadmindata = new Admin;
            $message = "Subadmin added succefully";
        }

        // Upload Image
        if($request->hasFile('image')){
            $image_tmp = $request->file('image');

            if($image_tmp->isValid()){

                //Create imageManager with desired driver
                $manager = new ImageManager(new Driver());

                //Read image from file system
                $image = $manager->read($image_tmp);

                //get image extention
                $extention = $image_tmp->getClientOriginalExtension();

                //Generate new image name
                $imageName = rand(111,99999).'.'.$extention;
                $image_path = public_path('/admin/images/profiles/'.$imageName);

                //Save image in specified path
                $image->save($image_path);   
            }
        }else if(!empty($data['current_image'])){
           $imageName = $data['current_image'];
            }else{
                $imageName = "";
            }
        
        $subadmindata->image = $imageName;
        $subadmindata->name = $data['name'];
        $subadmindata->mobile = $data['mobile'];

        if(!isset($data['id'])){
            $subadmindata->email = $data['email'];
            $subadmindata->role = 'subadmin';
            $subadmindata->status = 1;
        }
        if($data['password']!= ""){
            $subadmindata->password = bcrypt($data['password']);
        }
        $subadmindata->save();
        return array("message" => $message);
   }

   public function updateRole($request){
    $data = $request->all();

    // Remove existing Roles before updating
    AdminsRole::where('subadmin_id', $data['subadmin_id'])->delete();

    // Assign new roles dynamically
    foreach($data as $key => $value){
        if(!is_array($value)) continue; // Skip non-module fields

        $view = isset($value['view']) ? $value['view'] : 0;
        $edit = isset($value['edit']) ? $value['edit'] : 0;
        $full = isset($value['full']) ? $value['full'] : 0;

        AdminsRole::insert([
            'subadmin_id' => $data['subadmin_id'],
            'module' => $key,
            'view_access' => $view,
            'edit_access' => $edit,
            'full_access' => $full

        ]);
    }
    return ['message' => 'Sunadmin Roles Updated Successfully!'];
   }
    

}