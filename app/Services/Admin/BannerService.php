<?php

namespace App\Services\Admin;

use App\Models\Banner;
use App\Models\AdminsRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class BannerService
{
    /**
     * Get all banners.
     */
    public function Banners(){
        $admin = Auth::guard('admin')->user();
        $banners = Banner::orderBy('sort', 'ASC')->get();
        $bannersModuleCount = AdminsRole::where([
            'subadmin_id' => $admin->id,
            'module' => 'banners'
        ])->count();

        $bannersModule = [];

        if($admin->role == 'admin'){
            $bannersModule = [
                'view_access' => 1,
                'edit_access' => 1,
                'full_access' => 1,
            ];
        }elseif($bannersModuleCount == 0){
            return [
                'status '=> 'error',
                'message' => 'You do not have access to this module.'
            ];
        }else{
            $bannersModule = AdminsRole::where([
                'subadmin_id' => $admin->id,
                'module' => 'banners'
            ])->first()->toArray();
        }

        return [
            'status' => 'success',
            'banners' => $banners,
            'bannersModule' => $bannersModule
        ];
    }
    /**
     * Add or edit a banner.
     */

    public function addEditBanner($request){
        $data = $request->all();
        
        $banner = isset($data['id']) ? Banner::find($data['id']) : new Banner;

        $banner->type = $data['type'];
        $banner->link = $data['link'];
        $banner->title = $data['title'];
        $banner->alt = $data['alt'];
        $banner->sort = $data['sort'] ?? 0;
        $banner->status = $data['status'] ?? 1;

        // Handle image upload
        if ($request->hasFile('image')) {
           $path = 'front/images/banners/';
           if (!File::exists(public_path($path))) {
               File::makeDirectory(public_path($path), 0755, true);
           }
           // Delete old image if exists
           if(!empty($banner->image) && File::exists(public_path($path . $banner->image))) {
               File::delete(public_path($path . $banner->image));
           }

           $image = $request->file('image');
           $imageName = time() . '.' . $image->getClientOriginalExtension();
           $image->move(public_path($path), $imageName);
           $banner->image = $imageName;
         }

         $banner->save();

        return isset($data['id']) ? 'Banner updated successfully.' : 'Banner added successfully.';
    }

    /**
     * Update Banner Status via AJAX.
     */
    public function updateBannerStatus($data)
    {
        $status = ($data['status'] == 'Active') ? 0 : 1;
        Banner::where('id', $data['banner_id'])->update(['status' => $status]);
        return $status;
    }
    /**
     * Delete a banner.
     */

    public function deleteBanner($id)
    {
        $banner = Banner::findOrFail($id);
        $bannerImagePath = public_path('front/images/banners/' . $banner->image);
        if (File::exists($bannerImagePath)) {
            File::delete($bannerImagePath);
        }
        $banner->delete();
        return [
            'status' => 'success',
            'message' => 'Banner image deleted successfully.'
        ];
    }
}
