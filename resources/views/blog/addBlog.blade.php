@extends('layouts.main')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Add New Blog</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('blogs.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Bike Name</label>
                                    <select class="form-select" id="title" name="title" required>
                                        <option value="">— Select Bike —</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->name }}" {{ old('title') == $product->name ? 'selected' : '' }}>
                                                {{ $product->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-secondary-light">Bikes shown here come from Product Management.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="images" class="form-label">Images</label>
                                    <input type="file" class="form-control" id="images" name="images[]" multiple>
                                </div>
                            </div>
                        </div>
                        {{-- Description not needed — auto-generated from bike title on frontend
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
                        </div>
                        --}}
                        <button type="submit" class="btn btn-primary">Save Blog</button>
                        <a href="{{ route('manage.blogs') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection