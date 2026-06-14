@extends('layout.app')

@section('content')
    <div class="card">
        <div class="card-header">
            @can('create', App\Models\Publisher::class)
                <a href="{{ route('publishers.create') }}" class="btn btn-primary">Add Publisher</a>
            @endcan
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead><tr><th>Name</th><th>Website</th><th>Books</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse ($publishers as $publisher)
                        <tr>
                            <td><a href="{{ route('publishers.show', $publisher) }}">{{ $publisher->name }}</a></td>
                            <td>{{ $publisher->website ?? '-' }}</td>
                            <td>{{ $publisher->books_count }}</td>
                            <td>
                                @can('update', $publisher)
                                    <a href="{{ route('publishers.edit', $publisher) }}" class="btn btn-sm btn-secondary">Edit</a>
                                @endcan
                                @can('delete', $publisher)
                                    <form action="{{ route('publishers.destroy', $publisher) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Delete?');">@csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center">No publishers.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $publishers->links() }}
        </div>
    </div>
@endsection
