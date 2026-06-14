@extends('layout.app')

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Member Profile</h3>
                </div>
                <div class="card-body">
                    <strong><i class="fas fa-user mr-1"></i> Name</strong>
                    <p class="text-muted">{{ $member->name }}</p>
                    <hr>
                    <strong><i class="fas fa-envelope mr-1"></i> Email</strong>
                    <p class="text-muted">{{ $member->email }}</p>
                    <hr>
                    <strong><i class="fas fa-phone mr-1"></i> Phone</strong>
                    <p class="text-muted">{{ $member->phone ?? 'N/A' }}</p>
                    <hr>
                    <strong><i class="fas fa-calendar mr-1"></i> Membership Duration</strong>
                    <p class="text-muted">{{ $member->membership_duration }}</p>
                </div>
            </div>
        </div>

        <div class="card card-primary col-md-7 ml-2">
            <div class="card-header">
                <h3 class="card-title">Borrowing History</h3>
                <span class="badge bg-warning float-right">{{ $member->borrowings->count() }}</span>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Borrowed At</th>
                            <th>Due Date</th>
                            <th>Book Title</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($member->borrowings as $borrowing)
                            <tr>
                                <td>{{ $borrowing->borrowed_at?->format('Y-m-d') }}</td>
                                <td>{{ $borrowing->due_date?->format('Y-m-d') }}</td>
                                <td>
                                    <a href="{{ route('books.show', $borrowing->book_id) }}">
                                        {{ $borrowing->book->title }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $borrowing->status === 'overdue' ? 'danger' : ($borrowing->status === 'returned' ? 'success' : 'warning') }}">
                                        {{ ucfirst($borrowing->status) }}
                                    </span>
                                </td>
                                <td>
                                    @can('update', $borrowing)
                                        @if ($borrowing->returned_at === null)
                                            <form action="{{ route('borrowings.update', $borrowing) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-success">Return</button>
                                            </form>
                                        @else
                                            <span class="text-muted">Returned</span>
                                        @endif
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No borrowings.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card card-secondary col-md-11 ml-2 mt-3">
            <div class="card-header">
                <h3 class="card-title">Reviews</h3>
                <span class="badge bg-warning float-right">{{ $member->reviews->count() }}</span>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Rating</th>
                            <th>Comment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($member->reviews as $review)
                            <tr>
                                <td>{{ $review->rating }}/5</td>
                                <td>{{ $review->comment }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center">No reviews.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
