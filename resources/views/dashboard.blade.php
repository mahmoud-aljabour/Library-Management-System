@extends('layout.app')

@section('content')
    @if ($overdueCount > 0)
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <h5><i class="icon fas fa-exclamation-triangle"></i> Overdue Alert</h5>
            There {{ $overdueCount === 1 ? 'is' : 'are' }} <strong>{{ $overdueCount }}</strong> overdue
            {{ $overdueCount === 1 ? 'borrowing' : 'borrowings' }}.
            <a href="{{ route('borrowings.index', ['status' => 'overdue']) }}" class="alert-link">View overdue list</a>
        </div>
    @endif

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
            <div class="card-body p-0 table-responsive-stack">
                <table class="table table-sm mb-0">
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

    <div class="card table-card">
        <div class="card-header">
            <h3 class="card-title mb-0">Recent Borrowings</h3>
            <a href="{{ route('borrowings.index') }}" class="btn btn-sm btn-primary">View All</a>
        </div>
        <div class="card-body">
            @if ($borrowings->isEmpty())
                <x-empty-state icon="fas fa-exchange-alt" message="No borrowings yet." />
            @else
                <div class="table-responsive-stack">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Book</th>
                                <th>Member</th>
                                <th>Borrowed</th>
                                <th>Due</th>
                                <th>Status</th>
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
                                    <td><x-status-badge :status="$borrowing->status" /></td>
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
