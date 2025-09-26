<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Front\IndexService;

class IndexController extends Controller
{
    protected $indexService;
    public function __construct(IndexService $indexService)
    {
        $this->indexService = $indexService;
    }
    public function index()
    {
        // Logic for the front index page
        $banners = $this->indexService->getHomePageBanners();
        $featured = $this->indexService->featuredProducts();
        $newArrivals = $this->indexService->newArrivalsProducts();
        $categories = $this->indexService->homecategories();
        return view('front.index')
        ->with($banners)
        ->with($featured)
        ->with($newArrivals)
        ->with($categories);
    }
}
