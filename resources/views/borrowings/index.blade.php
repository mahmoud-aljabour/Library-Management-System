@extends('layout.app')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Borrowings</h3>
        </div>
        <div class="card-body">
            <form method="GET" class="row mb-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search book or member..."
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-control">
                        <option value="">All Statuses</option>
                        <option value="active" @selected(request('status') === 'active')>Active</option>
                        <option value="overdue" @selected(request('status') === 'overdue')>Overdue</option>
                        <option value="returned" @selected(request('status') === 'returned')>Returned</option>
                        <option value="borrowed" @selected(request('status') === 'borrowed')>Borrowed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('borrowings.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>

            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Book</th>
                        <th>Member</th>
                        <th>Borrowed</th>
                        <th>Due</th>
                        <th>Returned</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($borrowings as $borrowing)
                        <tr>
                            <td>
                                <a href="{{ route('books.show', $borrowing->book_id) }}">{{ $borrowing->book->title }}</a>
                            </td>
                            <td>
                                <a href="{{ route('members.show', $borrowing->member_id) }}">{{ $borrowing->member->name }}</a>
                            </td>
                            <td>{{ $borrowing->borrowed_at?->format('Y-m-d') }}</td>
                            <td>{{ $borrowing->due_date?->format('Y-m-d') }}</td>
                            <td>{{ $borrowing->returned_at?->format('Y-m-d') ?? '-' }}</td>
                            <td>
                                <span class="badge badge-{{ $borrowing->status === 'overdue' ? 'danger' : ($borrowing->status === 'returned' ? 'success' : 'warning') }}">
                                    {{ ucfirst($borrowing->status) }}
                                </span>
                            </td>
                            <td>
                                @can('update', $borrowing)
                                    @if ($borrowing->returned_at === null)
                                        <form action="{{ route('borrowings.update', $borrowing) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-sm btn-success">Return</button>
                                        </form>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No borrowings found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $borrowings->links() }}
        </div>
    </div>
@endsection
