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
                                    <label for="title" class="form-label">Title</label>
                                    <input type="text" class="form-control" id="title" name="title" value="{{ $blog->title }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="images" class="form-label">Add More Images</label>
                                    <input type="file" class="form-control" id="images" name="images[]" multiple>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="5" required>{{ $blog->description }}</textarea>
                        </div>
                        @if($blog->images)
                            <div class="mb-3">
                                <label class="form-label">Current Images</label>
                                <div class="d-flex gap-2 flex-wrap">
                                    @php $images = json_decode($blog->images, true); @endphp
                                    @foreach($images as $index => $image)
                                        <div class="position-relative">
                                            <img src="{{ asset($image) }}" alt="Blog Image" width="100" class="rounded">
                                            <div class="form-check position-absolute top-0 end-0">
                                                <input class="form-check-input" type="checkbox" name="delete_images[]" value="{{ $index }}" id="delete_{{ $index }}">
                                                <label class="form-check-label text-white bg-dark px-1 rounded" for="delete_{{ $index }}">
                                                    Delete
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
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