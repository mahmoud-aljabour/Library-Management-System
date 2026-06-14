@extends('layout.app')

@section('content')
    <div class="card card-primary col-md-8">
        <div class="card-header"><h3 class="card-title">Edit Member</h3></div>
        <form action="{{ route('members.update', $member) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $member->name) }}" required>
                    @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email', $member->email) }}" required>
                    @error('email')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $member->phone) }}">
                </div>
                <div class="form-group">
                    <label>Membership Date</label>
                    <input type="date" name="membership_date" class="form-control"
                        value="{{ old('membership_date', $member->membership_date?->format('Y-m-d')) }}" required>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active"
                        @checked(old('is_active', $member->is_active))>
                    <label class="form-check-label" for="is_active">Active Member</label>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('members.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
