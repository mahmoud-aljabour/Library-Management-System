@extends('layout.app')

@section('content')
    @if ($overdueCount > 0)
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <h5><i class="icon fas fa-exclamation-triangle"></i> Overdue Alert!</h5>
            There {{ $overdueCount === 1 ? 'is' : 'are' }} <strong>{{ $overdueCount }}</strong> overdue
            {{ $overdueCount === 1 ? 'borrowing' : 'borrowings' }}.
            <a href="{{ route('borrowings.index', ['status' => 'overdue']) }}" class="alert-link">View overdue list</a>
        </div>
    @endif

    <div class="container-fluid">
        <h5 class="mb-2">Library Overview</h5>
        <div class="row">
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-book"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Books</span>
                        <span class="info-box-number">{{ $totalBooks }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Members</span>
                        <span class="info-box-number">{{ $totalMembers }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-exchange-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Active Borrowings</span>
                        <span class="info-box-number">{{ $activeBorrowingsCount }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="fas fa-exclamation-triangle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Overdue Borrowings</span>
                        <span class="info-box-number">{{ $overdueCount }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if ($overdueBorrowings->isNotEmpty())
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">Overdue Items</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Book</th>
                                <th>Member</th>
                                <th>Due Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($overdueBorrowings as $borrowing)
                                <tr>
                                    <td>{{ $borrowing->book->title }}</td>
                                    <td>{{ $borrowing->member->name }}</td>
                                    <td>{{ $borrowing->due_date?->format('Y-m-d') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recent Borrowings</h3>
                <a href="{{ route('borrowings.index') }}" class="btn btn-sm btn-primary float-right">View All</a>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Book Title</th>
                            <th>Member Name</th>
                            <th>Borrowed Date</th>
                            <th>Due Date</th>
                            <th>Status</th>
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
                                <td>
                                    <span class="badge badge-{{ $borrowing->status === 'overdue' ? 'danger' : ($borrowing->status === 'returned' ? 'success' : 'warning') }}">
                                        {{ ucfirst($borrowing->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No borrowings yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $borrowings->links() }}
            </div>
        </div>
    </div>
@endsection
