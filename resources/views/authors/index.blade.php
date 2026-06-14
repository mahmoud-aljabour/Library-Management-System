@extends('layout.app')

@section('content')
    <div class="card">
        <div class="card-header">
            @can('create', App\Models\Author::class)
                <a href="{{ route('authors.create') }}" class="btn btn-primary">Add Author</a>
            @endcan
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr><th>Name</th><th>Nationality</th><th>Books</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @forelse ($authors as $author)
                        <tr>
                            <td><a href="{{ route('authors.show', $author) }}">{{ $author->name }}</a></td>
                            <td>{{ $author->nationality ?? '-' }}</td>
                            <td>{{ $author->books_count }}</td>
                            <td>
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
                    @empty
                        <tr><td colspan="4" class="text-center">No authors.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $authors->links() }}
        </div>
    </div>
@endsection
