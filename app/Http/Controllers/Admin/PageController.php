<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PageRequest;
use App\Services\Admin\PageService;
use App\Models\ColumnPreference;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class PageController extends Controller
{
    public $pageService;
    public function __construct(PageService $pageService)
    {
        $this->pageService = $pageService;
    }

    public function index()
    {
        Session::put('page', 'pages');
        $result = $this->pageService->pages();
        if($result['status'] === 'error'){
        return redirect('admin/dashboard')->with('error_message', $result['message']);
        }
        $pages = $result['pages'];
        $pagesModule = $result['pagesModule'];
        
        $columnPrefs = ColumnPreference::where('admin_id', Auth::guard('admin')->id())
        ->where('table_name', 'pages')
        ->first();

        $pagesSavedOrder = $columnPrefs ? json_decode($columnPrefs->column_order, true) : null;
        $pagesHiddenCols = $columnPrefs ? json_decode($columnPrefs->hidden_columns, true) : [];

        return view('admin.pages.index', compact('pages', 'pagesModule', 'pagesSavedOrder', 'pagesHiddenCols'));
    }

    public function create()
    {
        $title = 'Add Page';
        $page = new Page();
        return view('admin.pages.add_edit_page', compact('title', 'page'));
    }

    public function store(PageRequest $request)
    {
        $message = $this->pageService->addEditPage($request);
        return redirect()->route('pages.index')->with('success_message', $message);
    }

    public function edit(string $id)
    {
        $title = 'Edit Page';
        $page = Page::findOrFail($id);
        return view('admin.pages.add_edit_page', compact('title', 'page'));
    }

    public function update(PageRequest $request, string $id)
    {
        $request->merge(['id' => $id]);
        $message = $this->pageService->addEditPage($request);
        return redirect()->route('pages.index')->with('success_message', $message);
    }

    public function destroy(string $id)
    {
        $result = $this->pageService->deletePage($id);
        return redirect()->back()->with('success_message', $result['message']);
    }

    // In PageController.php
public function updatePageStatus(Request $request)
{
    try {
        if($request->ajax()){
            $data = $request->all();
            $status = $this->pageService->updatePageStatus($data);
            return response()->json([
                'success' => true,
                'status' => $status,
                'page_id' => $data['page_id']
            ]);
        }
        return response()->json(['success' => false, 'message' => 'Invalid request']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
}
}
