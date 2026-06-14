@extends('layout.app')

@section('content')
    <div class="card">
        <div class="card-header">
            @can('create', App\Models\Member::class)
                <a href="{{ route('members.create') }}" class="btn btn-primary">Add Member</a>
            @endcan
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Active Borrows</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($members as $member)
                        <tr>
                            <td><a href="{{ route('members.show', $member) }}">{{ $member->name }}</a></td>
                            <td>{{ $member->email }}</td>
                            <td>{{ $member->phone ?? '-' }}</td>
                            <td>{{ $member->active_borrowings_count }}</td>
                            <td>
                                <span class="badge badge-{{ $member->is_active ? 'success' : 'secondary' }}">
                                    {{ $member->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                @can('update', $member)
                                    <a href="{{ route('members.edit', $member) }}" class="btn btn-sm btn-secondary">Edit</a>
                                    <form action="{{ route('members.toggle-status', $member) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-warning">
                                            {{ $member->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                @endcan
                                @can('delete', $member)
                                    <form action="{{ route('members.destroy', $member) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Delete this member?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No members found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $members->links() }}
        </div>
    </div>
@endsection
