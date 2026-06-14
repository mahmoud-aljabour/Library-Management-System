@extends('layout.app')

@section('content')
    <div class="card table-card">
        <div class="card-header">
            <span class="text-muted">{{ $categories->total() }} category(ies)</span>
            @can('create', App\Models\Category::class)
                <a href="{{ route('categories.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus mr-1"></i> Add Category
                </a>
            @endcan
        </div>
        <div class="card-body">
            @if ($categories->isEmpty())
                <x-empty-state icon="fas fa-tags" message="No categories found." :action-url="route('categories.create')" action-label="Add category" />
            @else
                <div class="table-responsive-stack">
                    <table class="table table-bordered table-hover">
                        <thead><tr><th>Name</th><th>Description</th><th>Books</th><th>Actions</th></tr></thead>
                        <tbody>
                            @foreach ($categories as $category)
                                <tr>
                                    <td><a href="{{ route('categories.show', $category) }}">{{ $category->name }}</a></td>
                                    <td>{{ $category->description ? \Illuminate\Support\Str::limit($category->description, 50) : '-' }}</td>
                                    <td>{{ $category->books_count }}</td>
                                    <td class="text-nowrap">
                                        @can('update', $category)
                                            <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-secondary">Edit</a>
                                        @endcan
                                        @can('delete', $category)
                                            <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline"
                                                onsubmit="return confirm('Delete this category?');">@csrf @method('DELETE')
                                                <button class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $categories->links() }}
            @endif
        </div>
    </div>
@endsection
