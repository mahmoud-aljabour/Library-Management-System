@extends('layout.app')

@section('content')
    <div class="card table-card">
        <div class="card-header">
            <span class="text-muted">{{ $books->total() }} book(s)</span>
            @can('create', App\Models\Book::class)
                <a class="btn btn-primary btn-sm" href="{{ route('books.create') }}">
                    <i class="fas fa-plus mr-1"></i> Add Book
                </a>
            @endcan
        </div>

        <div class="card-body">
            <form action="{{ route('books.index') }}" method="GET" class="row mb-3 filter-bar">
                <div class="col-md-5">
                    <input type="text" name="title" value="{{ request('title') }}" class="form-control"
                        placeholder="Search by title">
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-control">
                        <option value="">All Statuses</option>
                        <option value="available" @selected(request('status') === 'available')>Available</option>
                        <option value="borrowed" @selected(request('status') === 'borrowed')>Borrowed</option>
                        <option value="reserved" @selected(request('status') === 'reserved')>Reserved</option>
                        <option value="archived" @selected(request('status') === 'archived')>Archived</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('books.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>

            @if ($books->isEmpty())
                <x-empty-state
                    icon="fas fa-book"
                    message="No books found."
                />
                @can('create', App\Models\Book::class)
                    <div class="text-center">
                        <a href="{{ route('books.create') }}" class="btn btn-sm btn-outline-primary">Add your first book</a>
                    </div>
                @endcan
            @else
                <div class="table-responsive-stack">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Status</th>
                                <th>Copies</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($books as $book)
                                <tr>
                                    <td>
                                        <a href="{{ route('books.show', $book) }}">{{ $book->title }}</a>
                                    </td>
                                    <td>{{ $book->author->name }}</td>
                                    <td><x-status-badge :status="$book->status" /></td>
                                    <td>
                                        <span class="badge badge-{{ $book->available_copies > 0 ? 'info' : 'secondary' }}">
                                            {{ $book->available_copies }} / {{ $book->total_copies }}
                                        </span>
                                    </td>
                                    <td class="text-right text-nowrap">
                                        @can('update', $book)
                                            <a href="{{ route('books.edit', $book) }}" class="btn btn-sm btn-secondary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endcan
                                        @can('delete', $book)
                                            <form action="{{ route('books.destroy', $book) }}" method="POST" class="d-inline"
                                                onsubmit="return confirm('Are you sure you want to delete this book?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{ $books->withQueryString()->links() }}
            @endif
        </div>
    </div>
@endsection
