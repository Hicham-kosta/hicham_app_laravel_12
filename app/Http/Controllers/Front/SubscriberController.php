<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscriber;
use App\Services\Front\SubscriberService;
use App\Http\Requests\Front\SubscriberRequest;

class SubscriberController extends Controller
{
    protected $service;

    public function __construct(SubscriberService $service)
    {
        $this->service = $service;
    }

    public function store(SubscriberRequest $request)
    {
        $result = $this->service->addSubscriber($request->email);

        if($request->ajax()){
            return response()->json($result);
        }

        return $result['status'] 
        ? redirect()->back()->with('success', $result['message'])
        : redirect()->back()->with('error', $result['message']);
    }
}
