@extends('layouts.main')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit Blog</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('blogs.update', $blog) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Bike Name</label>
                                    @php
                                        $titleMatchesProduct = $products->pluck('name')->contains($blog->title);
                                    @endphp
                                    <select class="form-select" id="title" name="title" required>
                                        <option value="">— Select Bike —</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->name }}" {{ old('title', $blog->title) == $product->name ? 'selected' : '' }}>
                                                {{ $product->name }}
                                            </option>
                                        @endforeach
                                        @if(!$titleMatchesProduct)
                                            <option value="{{ $blog->title }}" selected>{{ $blog->title }}</option>
                                        @endif
                                    </select>
                                    <small class="text-secondary-light">Bikes shown here come from Product Management.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="images" class="form-label">Add More Images</label>
                                    <input type="file" class="form-control" id="images" name="images[]" multiple>
                                </div>
                            </div>
                        </div>

                        {{-- Current images with delete option --}}
                        @if($blog->images)
                            @php $images = json_decode($blog->images, true); @endphp
                            @if(!empty($images))
                            <div class="mb-3">
                                <label class="form-label">Current Images</label>
                                <div class="d-flex gap-2 flex-wrap">
                                    @foreach($images as $index => $image)
                                        <div class="position-relative">
                                            <img src="{{ asset($image) }}" alt="Blog Image"
                                                 width="100" class="rounded">
                                            <div class="form-check position-absolute top-0 end-0">
                                                <input class="form-check-input" type="checkbox"
                                                       name="delete_images[]" value="{{ $index }}"
                                                       id="delete_{{ $index }}">
                                                <label class="form-check-label text-white bg-dark px-1 rounded"
                                                       for="delete_{{ $index }}">
                                                    Delete
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        @endif

                        <button type="submit" class="btn btn-primary">Update Blog</button>
                        <a href="{{ route('manage.blogs') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection