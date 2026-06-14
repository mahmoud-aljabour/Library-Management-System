@extends('layout.app')

@section('content')
    <div class="card table-card">
        <div class="card-header">
            <span class="text-muted">{{ $authors->total() }} author(s)</span>
            @can('create', App\Models\Author::class)
                <a href="{{ route('authors.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus mr-1"></i> Add Author
                </a>
            @endcan
        </div>
        <div class="card-body">
            @if ($authors->isEmpty())
                <x-empty-state icon="fas fa-pen-fancy" message="No authors found." :action-url="route('authors.create')" action-label="Add author" />
            @else
                <div class="table-responsive-stack">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr><th>Name</th><th>Nationality</th><th>Books</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($authors as $author)
                                <tr>
                                    <td><a href="{{ route('authors.show', $author) }}">{{ $author->name }}</a></td>
                                    <td>{{ $author->nationality ?? '-' }}</td>
                                    <td>{{ $author->books_count }}</td>
                                    <td class="text-nowrap">
                                        @can('update', $author)
                                            <a href="{{ route('authors.edit', $author) }}" class="btn btn-sm btn-secondary">Edit</a>
                                        @endcan
                                        @can('delete', $author)
                                            <form action="{{ route('authors.destroy', $author) }}" method="POST" class="d-inline"
                                                onsubmit="return confirm('Delete this author?');">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $authors->links() }}
            @endif
        </div>
    </div>
@endsection
