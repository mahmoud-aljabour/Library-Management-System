@extends('layout.app')

@section('content')
    <div class="card card-primary col-md-8">
        <div class="card-header"><h3 class="card-title">Edit Publisher</h3></div>
        <form action="{{ route('publishers.update', $publisher) }}" method="POST">
            @csrf @method('PUT')
            <div class="card-body">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $publisher->name) }}" required>
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address', $publisher->address) }}">
                </div>
                <div class="form-group">
                    <label>Website</label>
                    <input type="url" name="website" class="form-control" value="{{ old('website', $publisher->website) }}">
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('publishers.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
