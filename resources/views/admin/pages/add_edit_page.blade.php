@extends('admin.layout.layout')

@section('content')

<!-- CKEditor height fix -->
<style>
.ck-editor__editable_inline {
    min-height: 200px !important;
}
</style>

<main class="app-main">

    <!-- Page Header -->
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">CMS Pages</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item">
                            <a href="{{ url('admin/dashboard') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item active">{{ $title }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Page Content -->
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">

                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h5 class="card-title">{{ $title }}</h5>
                        </div>

                        <!-- Alerts -->
                        @if(Session::has('error_message'))
                            <div class="alert alert-danger m-3">
                                {{ Session::get('error_message') }}
                            </div>
                        @endif

                        @if(Session::has('success_message'))
                            <div class="alert alert-success m-3">
                                {{ Session::get('success_message') }}
                            </div>
                        @endif

                        @foreach ($errors->all() as $error)
                            <div class="alert alert-danger m-3">{{ $error }}</div>
                        @endforeach

                        <!-- Form -->
                        <form method="POST"
                              action="{{ isset($page->id) ? route('pages.update', $page->id) : route('pages.store') }}">
                            @csrf
                            @if(isset($page->id))
                                @method('PUT')
                            @endif

                            <div class="card-body">

                                <div class="mb-3">
                                    <label class="form-label">Page Title *</label>
                                    <input type="text"
                                           name="title"
                                           class="form-control"
                                           value="{{ old('title', $page->title ?? '') }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Page URL (Slug) *</label>
                                    <input type="text"
                                           name="url"
                                           class="form-control"
                                           value="{{ old('url', $page->url ?? '') }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Page Content</label>
                                    <textarea name="description"
                                              id="description"
                                              class="form-control">
{{ old('description', $page->description ?? '') }}
                                    </textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Meta Title</label>
                                        <input type="text"
                                               name="meta_title"
                                               class="form-control"
                                               value="{{ old('meta_title', $page->meta_title ?? '') }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Meta Keywords</label>
                                        <input type="text"
                                               name="meta_keywords"
                                               class="form-control"
                                               value="{{ old('meta_keywords', $page->meta_keywords ?? '') }}">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Meta Description</label>
                                    <input type="text"
                                           name="meta_description"
                                           class="form-control"
                                           value="{{ old('meta_description', $page->meta_description ?? '') }}">
                                </div>

                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Sort Order</label>
                                        <input type="number"
                                               name="sort_order"
                                               class="form-control"
                                               value="{{ old('sort_order', $page->sort_order ?? 0) }}">
                                    </div>

                                    <div class="col-md-3 mb-3 d-flex align-items-end">
                                        <div class="form-check">
                                            <input type="checkbox"
                                                   class="form-check-input"
                                                   name="status"
                                                   value="Active"
                                                   @checked(old('status', $page->status ?? 'Active'))>
                                            <label class="form-check-label">Active</label>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ route('pages.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>

                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

</main>

<!-- CKEditor CDN -->
<script src="https://cdn.ckeditor.com/ckeditor5/41.3.1/classic/ckeditor.js"></script>

<!-- CKEditor Init -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    ClassicEditor
        .create(document.querySelector('#description'))
        .then(editor => {
            editor.ui.view.editable.element.style.minHeight = '200px';
        })
        .catch(error => {
            console.error(error);
        });
});
</script>
@endsection
