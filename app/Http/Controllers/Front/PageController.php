<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\Front\PageService;
use Illuminate\Http\Request;

class PageController extends Controller
{
    protected PageService $pageService;
    public function __construct(PageService $pageService)
    {
        $this->pageService = $pageService;
    }

    /**
     * Display CMS page by slug(url)
     * 
     * @param string $slug
     */
    public function show(string $url)
    {
        $result = $this->pageService->getPageByUrl($url);
        if($result['status'] === 'error') {
            abort(404, $result['message']);
        }

        $page = $result['page'];

        //Optionally pass more SEO/meta
        return view('front.pages.show', compact('page'));
    }
}