@extends('front.layout.layout')
@section('title', $page->meta_title?: $page->title)
@section('meta_description', $page->meta_description?: '')
@section('meta_keywords', $page->meta_keywords?: '')

@section('content')
<!-- Page Header Start -->
 <div class="container-fluid bg-secondary mb-5">
    <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 150px">
        <h1 class="font-weight-bold text-uppercase mb-3">{{ $page->title }}</h1>
        <div class="d-inline-flex">
            <p class="m-0"><a href="{{url('/')}}">Home</a></p>
            <p class="m-0 px-2"></p>
            <p class="m-0">{{ $page->title }}</p>
        </div>
    </div>
</div>
<!-- Page Header End -->

<!-- CMS Content Start -->
 <div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="cms-description">
                        {!! $page->description !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
 </div>
 <!-- CMS Content End -->
  @endsection