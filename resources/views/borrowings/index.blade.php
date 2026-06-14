@extends('layout.app')

@section('content')
    <div class="card table-card">
        <div class="card-header">
            <span class="text-muted">{{ $borrowings->total() }} record(s)</span>
        </div>
        <div class="card-body">
            <form method="GET" class="row mb-3 filter-bar">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control" placeholder="Search book or member..."
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-control">
                        <option value="">All Statuses</option>
                        <option value="active" @selected(request('status') === 'active')>Active</option>
                        <option value="overdue" @selected(request('status') === 'overdue')>Overdue</option>
                        <option value="returned" @selected(request('status') === 'returned')>Returned</option>
                        <option value="borrowed" @selected(request('status') === 'borrowed')>Borrowed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('borrowings.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>

            @if ($borrowings->isEmpty())
                <x-empty-state icon="fas fa-exchange-alt" message="No borrowings found." />
            @else
                <div class="table-responsive-stack">
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
                            @foreach ($borrowings as $borrowing)
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
                                    <td><x-status-badge :status="$borrowing->status" /></td>
                                    <td>
                                        @can('update', $borrowing)
                                            @if ($borrowing->returned_at === null)
                                                <form action="{{ route('borrowings.update', $borrowing) }}" method="POST" class="d-inline"
                                                    onsubmit="return confirm('Mark this book as returned?');">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-sm btn-success">
                                                        <i class="fas fa-undo mr-1"></i> Return
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $borrowings->links() }}
            @endif
        </div>
    </div>
@endsection
