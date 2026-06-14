@extends('layout.app')

@section('content')
    <div class="card table-card">
        <div class="card-header">
            <span class="text-muted">{{ $publishers->total() }} publisher(s)</span>
            @can('create', App\Models\Publisher::class)
                <a href="{{ route('publishers.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus mr-1"></i> Add Publisher
                </a>
            @endcan
        </div>
        <div class="card-body">
            @if ($publishers->isEmpty())
                <x-empty-state icon="fas fa-building" message="No publishers found." :action-url="route('publishers.create')" action-label="Add publisher" />
            @else
                <div class="table-responsive-stack">
                    <table class="table table-bordered table-hover">
                        <thead><tr><th>Name</th><th>Website</th><th>Books</th><th>Actions</th></tr></thead>
                        <tbody>
                            @foreach ($publishers as $publisher)
                                <tr>
                                    <td><a href="{{ route('publishers.show', $publisher) }}">{{ $publisher->name }}</a></td>
                                    <td>{{ $publisher->website ?? '-' }}</td>
                                    <td>{{ $publisher->books_count }}</td>
                                    <td class="text-nowrap">
                                        @can('update', $publisher)
                                            <a href="{{ route('publishers.edit', $publisher) }}" class="btn btn-sm btn-secondary">Edit</a>
                                        @endcan
                                        @can('delete', $publisher)
                                            <form action="{{ route('publishers.destroy', $publisher) }}" method="POST" class="d-inline"
                                                onsubmit="return confirm('Delete this publisher?');">@csrf @method('DELETE')
                                                <button class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $publishers->links() }}
            @endif
        </div>
    </div>
@endsection
