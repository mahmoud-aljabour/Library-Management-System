@extends('layout.app')

@section('content')
    <div class="card">
        <div class="card-header">
            @can('create', App\Models\Category::class)
                <a href="{{ route('categories.create') }}" class="btn btn-primary">Add Category</a>
            @endcan
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead><tr><th>Name</th><th>Description</th><th>Books</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td><a href="{{ route('categories.show', $category) }}">{{ $category->name }}</a></td>
                            <td>{{ $category->description ? \Illuminate\Support\Str::limit($category->description, 50) : '-' }}</td>
                            <td>{{ $category->books_count }}</td>
                            <td>
                                @can('update', $category)
                                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-secondary">Edit</a>
                                @endcan
                                @can('delete', $category)
                                    <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Delete?');">@csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center">No categories.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $categories->links() }}
        </div>
    </div>
@endsection
