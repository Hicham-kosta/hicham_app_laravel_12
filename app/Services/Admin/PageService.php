<?php

namespace App\Services\Admin;

use App\Models\Page;
use App\Models\AdminsRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PageService
{
    public function pages(): array
    {
    $pages = Page::query()->latest()->get();

    $admin = Auth::guard('admin')->user();
    $status = "success"; $message = ""; $pagesModule = [];

    if ($admin->role === 'admin') {
        $pagesModule = ['view_access' => 1, 'edit_access' => 1, 'full_access' => 1];
    } else {
        $count = AdminsRole::where(['subadmin_id' => $admin->id, 'module' => 'pages'])->count();
        if ($count === 0) {
            $status = "error";
            $message = "You do not have access to this module";
        } else {
            $pagesModule = AdminsRole::where(['subadmin_id' => $admin->id, 'module' => 'pages'])
                ->first()
                ->toArray();
        }
    }
    return compact('pages', 'pagesModule', 'status', 'message');
    }

    public function addEditPage($request): string
    {
        $data = $request->all();
        
        if(!empty($data['id'])){
            $page = Page::findOrFail($data['id']);
            $message = "Page updated successfully";   
        }else{
            $page = new Page();
            $message = "Page added successfully";   
        }
        $page->title = trim($data['title']);
        $page->url = $this->slugify($data['url']);
        $page->description = $data['description'] ?? null;
        $page->meta_title = $data['meta_title'] ?? null;
        $page->meta_description = $data['meta_description'] ?? null;
        $page->meta_keywords = $data['meta_keywords'] ?? null;
        $page->sort_order = (int)($data['sort_order'] ?? 0);
        $page->status = isset($data['status']) ? 1 : 0;

        $page->save();
        return $message;
    }

    public function deletePage(string $id): array
    {
        Page::where('id', $id)->delete();
        return ['message' => "Page deleted successfully"];
    }
    // In your PageService class, add this method:
public function updatePageStatus(array $data): int
{
    $status = ($data['status'] === "Active") ? 0 : 1;
    Page::where('id', $data['page_id'])->update(['status' => $status]);
    return $status; // Fixed: was returning "status" (undefined) instead of $status
}

    private function slugify(string $value): string
    {
        $slug = Str::slug($value,'-');
        return $slug?: Str::random(6);
    }

}
