<?php

namespace App\Services\Admin;

use App\Models\Category;
use App\Models\AdminsRole;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Auth;

Class CategoryService{

    public function categories(){

        $categories = Category::with('parentcategory')->get();
        $admin = Auth::guard('admin')->user();
        $status = 'success';
        $message = "";
        $categoriesModule = [];

        // Admin has fulle access
        if($admin->role == "admin"){
            $categoriesModule = [
                'view_access' => 1,
                'edit_access' => 1,
                'full_access' => 1
            ];
        }else{
            $categoriesModuleCount = AdminsRole::where([
                'subadmin_id' => $admin->id,
                'module' => 'categories'
            ])->count();
            if($categoriesModuleCount == 0){
                $status = "error";
                $message = "This feature is restrected for you!";
            }else{
                $categoriesModule = AdminsRole::where([
                    'subadmin_id' => $admin->id,
                    'module' => 'categories'
                ])->first()->toArray();
            }
        }
        return [
            "categories" => $categories,
            "categoriesModule" => $categoriesModule,
            "status" => $status,
            "message" => $message
        ];
    }

    public function addEditCategory($request){

        $data = $request->all();

        if(isset($data['id']) && $data['id']!= ""){
            // Edit Category
            $category = Category::find($data['id']);
            $message = "Category Updated Successfully!";
        }else{
            // Add Category
            $category = new Category;
            $message = "Category Added Successfully!";
        }

        // Save parent_id (null for Main Category)
        $category->parent_id = !empty($data['parent_id']) ? $data['parent_id'] : null;

        // Upload Category Image
        if($request->hasFile('category_image')){
            $image_tmp = $request->file('category_image');
            if($image_tmp->isValid()){
                $manager = new ImageManager(new Driver());
                $image = $manager->read($image_tmp);
                $extension = $image_tmp->getClientOriginalExtension();
                $imageName = rand(111,99999).'.'.$extension;
                $image_path = public_path('front/images/categories/'.$imageName);
                $image->save($image_path);
                $category->image = $imageName;
            }
        }

        // Upload Size Chart
        if($request->hasFile('size_chart')){
            $sizechart_tmp = $request->file('size_chart');
            if($sizechart_tmp->isValid()){
                $manager = new ImageManager(new Driver());
                $image = $manager->read($sizechart_tmp);
                $sizechart_extension = $sizechart_tmp->getClientOriginalExtension();
                $sizechartimageName = rand(111,99999).'.'.$sizechart_extension;
                $sizechart_image_path = public_path('front/images/sizecharts/'.$sizechartimageName);
                $image->save($sizechart_image_path);
                $category->size_chart =  $sizechartimageName;
            }
        }

        //Format Name and URL
        $data['category_name'] = str_replace("-"," ",ucwords(strtolower($data['category_name'])));
        $data['url'] = str_replace(" ","-",strtolower($data['url']));
        $category->name = $data['category_name'];

        // Discount Default
        if(empty($data['category_discount'])){
            $data['category_discount'] = 0;
        }

        $category->discount = $data['category_discount'];
        $category->description = $data['description'];
        $category->url = $data['url'];
        $category->meta_title = $data['meta_title'];
        $category->meta_description = $data['meta_description'];
        $category->meta_keywords = $data['meta_keywords'];

        // Status Manu
        if(!empty($data['status'])){
            $data['status'] = 1;
        }else{
            $data['status'] = 0;
        }

        //status Default
        $category->status = 1;

        $category->save();

        return $message;
    }

    public function updateCategoryStatus($data){
        $status = ($data['status'] == "Active") ? 0 : 1;
        Category::where('id', $data['category_id'])->update(['status' => $status]);
        return $status;
    }

    public function deleteCategory($id){
        Category::where('id', $id)->delete();
        $message = 'Category Deleted Successfully';
        return ['message' => $message];
    }

    public function deleteCategoryImage($categoryId){
        $categoryImage = Category::where('id', $categoryId)->value('image');
        if($categoryImage){
            $category_image_path = 'front/images/categories/'.$categoryImage;
            if(file_exists(public_path($category_image_path))) {
                unlink(public_path($category_image_path)); // Delete the image from the server
            }
            Category::where('id', $categoryId)->update(['image' => null]); // Update the database to remove the image path
            return ['status' => true, 'message' => 'image deleted successfully!'];
        }
            return ['status' => false, 'message' => 'image not found!'];
    }

    public function deleteSizeChartImage($categoryId){
        $sizechartImage = Category::where('id', $categoryId)->value('size_chart');
        if($sizechartImage){
            $sizechart_image_path = 'front/images/sizecharts/'.$sizechartImage;
            if(file_exists(public_path($sizechart_image_path))) {
                unlink(public_path($sizechart_image_path)); // Delete the image from the server
            }
            Category::where('id', $categoryId)->update(['size_chart' => null]); // Update the database to remove the image path
            return ['status' => true, 'message' => 'sizechart image deleted successfully!'];
        }
            return ['status' => false, 'message' => 'size Chart image not found!'];
    }

}