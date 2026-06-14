@extends('layout.app')

@section('content')
    <div class="card table-card">
        <div class="card-header">
            <span class="text-muted">{{ $members->total() }} member(s)</span>
            @can('create', App\Models\Member::class)
                <a href="{{ route('members.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus mr-1"></i> Add Member
                </a>
            @endcan
        </div>
        <div class="card-body">
            @if ($members->isEmpty())
                <x-empty-state
                    icon="fas fa-users"
                    message="No members found."
                    :action-url="route('members.create')"
                    action-label="Add member"
                />
            @else
            <div class="table-responsive-stack">
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
                    @foreach ($members as $member)
                        <tr>
                            <td><a href="{{ route('members.show', $member) }}">{{ $member->name }}</a></td>
                            <td>{{ $member->email }}</td>
                            <td>{{ $member->phone ?? '-' }}</td>
                            <td>{{ $member->active_borrowings_count }}</td>
                            <td>
                                <x-status-badge :status="$member->is_active ? 'active' : 'inactive'" />
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
                    @endforeach
                </tbody>
            </table>
            </div>
            {{ $members->links() }}
            @endif
        </div>
    </div>
@endsection
