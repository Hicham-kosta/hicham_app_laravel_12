@extends('admin.layout.layout')
@section('content')
<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Banner Management</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{$title}}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col g-4">
                <div class="col-md-8">
                    <div class="card card-primary card-outline mb-4">
                        <div class="card-header">
                            <div class="card-title">{{$title}}</div>
                        </div>
                        @if(Session::has('error_message'))
                        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                            <strong>Error</strong> {{Session::get('error_message')}}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif
                        @if (Session::has('success_message'))
                        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                            <strong>Success</strong> {{Session::get('success_message')}}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif
                        @foreach ($errors->all() as $error)
                        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                            <strong>Error</strong> {!!$error!!}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endforeach
                        <form action="{{ isset($banner->id) ? route('banners.update', $banner->id) : route('banners.store') }}" method="POST" enctype="multipart/form-data">
                         @csrf
                         @if(isset($banner->id))
                          @method('PUT')
                         @endif
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="type">Banner Type*</label>
                                    <option value="">Select Type</option>
                                    <select class="form-control" name="type">
                                        <option value=""></option>
                                        <option value="Slider" {{old('type', $banner->type ?? '') == 'slider' ? 
                                        'selected' : ''}}>Slider</option>
                                        <option value="Fix" {{old('type', $banner->type ?? '') == 'Fix' ? 
                                        'selected' : ''}}>Fix</option>
                                        <option value="logo" {{old('type', $banner->type ?? '') == 'logo' ? 
                                        'selected' : ''}}>Logo</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="title">Banner Title*</label>
                                    <input type="text" class="form-control" name="title" value="{{old('title', 
                                        $banner->title ?? '')}}">
                                </div>
                                <div class="mb-3">
                                    <label for="alt">Alt Text</label>
                                    <input type="text" class="form-control" name="alt" value="{{old('alt', 
                                        $banner->alt ?? '')}}">
                                </div>
                                <div class="mb-3">
                                    <label for="link">Link</label>
                                    <input type="text" class="form-control" name="link" value="{{old('link', 
                                        $banner->link ?? '')}}">
                                </div>
                                <div class="mb-3">
                                    <label for="sort">Sort Order</label>
                                    <input type="number" class="form-control" name="sort" value="{{old('sort', 
                                        $banner->sort ?? 0)}}">
                                </div>
                                <div class="mb-3">
                                    <label for="alt">Banner Image @if(!isset($banner->id)) * @endif</label>
                                    <input type="file" class="form-control" name="image">
                                    @if(!empty($banner->image))
                                    <div class="mt-2">
                                        <img src="{{ asset('front/images/banners/' . $banner->image) }}" 
                                            width="100" alt="banner">
                                    </div>
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <label>Status</label><br>
                                    <input type="checkbox" name="status" value="1" {{old('status', 
                                        $banner->status ?? 1) == 1 ? 'checked' : ''}}> Active
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection