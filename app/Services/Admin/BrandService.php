<?php

namespace App\Services\Admin;

use App\Models\Brand;
use App\Models\AdminsRole;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Auth;

class BrandService
{
    public function brands(){

        $brands = Brand::get();
        $admin = Auth::guard('admin')->user();
        $status = "success";
        $message = "";
        $brandsModule = [];

        // Admin has full Access
        if($admin->role = "admin"){
            $brandsModule = [
                'view_access' => 1,
                'edit_access' => 1,
                'full_access' => 1
            ];
        } else{
            $brandsModuleCount = AdminsRole::where([
                'subadmin_id' => $admin->id,
                'module' => 'brands'
            ])->count();

            if($brandsModuleCount == 0){
                $status = "error";
                $message = "You do not have access to this module";
            } else {
                $brandsModule = AdminsRole::where([
                    'subadmin_id' => $admin->id,
                    'module' => 'brands'
                ])->first()->toArray;
            }
        }
        return [
            'status' => $status,
            'message' => $message,
            'brands' => $brands,
            'brandsModule' => $brandsModule
        ];
    }

    public function updateBrandStatus($data)
    {
        $status = ($data['status'] == "Active") ? 0 : 1;
        Brand::where('id', $data['brand_id'])->update(['status' => $status]);
        return $status;
    }

    public function deleteBrand($id){
        Brand::where('id', $id)->delete();
        $message = "Brand has been deleted successfully";
        return ['message' => $message];
    }

    public function addEditBrand($request){

        $data = $request->all();
        $brand = new Brand;

        if(isset($data['id']) && ($data['id'] != "")){
            // Edit Brand
            $brand = Brand::find($data['id']);
            $message = "Brand has been updated successfully";
        }else {
            // Add Brand
            $brand = new Brand;
            $message = "Brand has been added successfully";
        }
        // Upload Brand image
        if($request->hasFile('image')){
            $image_tmp = $request->file('image');
            if($image_tmp->isValid()){
                $manager = new imageManager(new Driver());
                $image = $manager->read($image_tmp);
                $extension = $image_tmp->getClientOriginalExtension();
                $imageName = rand(111, 99999) . '.' . $extension;
                $imagePath = 'front/images/brands/' . $imageName;
                $image->save(public_path($imagePath));
                $brand->image = $imageName;
            }
        }

        // Upload Brand logo
        if($request->hasFile('logo')){
            $logo_tmp = $request->file('logo');
            if($logo_tmp->isValid()){
                $manager = new imageManager(new Driver());
                $logo = $manager->read($logo_tmp);
                $logo_extension = $logo_tmp->getClientOriginalExtension();
                $logoName = rand(111, 99999) . '.' . $logo_extension;
                $logoPath = 'front/images/logos/' . $logoName;
                $logo->save(public_path($logoPath));
                $brand->logo = $logoName;
            }
        }

        // Format Name and URL
        $data['name'] = str_replace('-', ' ', ucwords(strtolower($data['name'])));
        $data['url'] = str_replace(' ', '-', strtolower($data['url']));
        $brand->name = $data['name'];

        // Discount Default
        if(empty($data['brand_discount'])){
            $data['brand_discount'] = 0;
        }

        $brand->discount = $data['brand_discount'];
        $brand->url = $data['url'];
        $brand->description = $data['description'];
        $brand->meta_title = $data['meta_title'];
        $brand->meta_description = $data['meta_description'];
        $brand->meta_keywords = $data['meta_keywords'];
        
        // Menu Status
        if(!empty($data['menu_status'])){
            $brand->menu_status = 1;
        } else {
            $brand->menu_status = 0;
        }

        $brand->status = 1; // Default status is Active

        $brand->save();

        return $message;
    }
}