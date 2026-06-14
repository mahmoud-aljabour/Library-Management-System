@extends('layout.app')

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">{{ $book->title }}</h3>
                    <div class="card-tools">
                        <x-status-badge :status="$book->status" />
                        @can('update', $book)
                            <a href="{{ route('books.edit', $book) }}" class="btn btn-sm btn-secondary ml-2">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        @endcan
                    </div>
                </div>

                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">ISBN</dt>
                        <dd class="col-sm-8">{{ $book->isbn ?? '-' }}</dd>

                        <dt class="col-sm-4">Edition</dt>
                        <dd class="col-sm-8">{{ $book->edition ?? '-' }}</dd>

                        <dt class="col-sm-4">Description</dt>
                        <dd class="col-sm-8">{{ $book->description ?? '-' }}</dd>

                        <dt class="col-sm-4">Language</dt>
                        <dd class="col-sm-8">{{ $book->language ?? '-' }}</dd>

                        <dt class="col-sm-4">Available Copies</dt>
                        <dd class="col-sm-8">
                            <span class="badge badge-{{ $book->available_copies > 0 ? 'info' : 'secondary' }}">
                                {{ $book->available_copies }} / {{ $book->total_copies }}
                            </span>
                        </dd>
                    </dl>

                    <div class="mt-3">
                        @can('create', App\Models\Borrowing::class)
                            @if ($book->available_copies > 0)
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#borrowModal">
                                    <i class="fas fa-hand-holding mr-1"></i> Borrow this book
                                </button>
                            @else
                                <span class="badge badge-secondary">No copies available</span>
                            @endif
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title">Author</h3>
                </div>
                <div class="card-body">
                    <p class="mb-1">
                        <strong>
                            <a href="{{ route('authors.show', $book->author) }}">{{ $book->author->name }}</a>
                        </strong>
                    </p>
                    <p class="text-muted mb-1">{{ $book->author->nationality ?? 'Nationality not set' }}</p>
                    <p class="mb-0">{{ $book->author->bio ?? 'No bio available.' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="borrowModal" tabindex="-1" role="dialog" aria-labelledby="borrowModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="borrowModalLabel">Borrow: {{ $book->title }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('borrowings.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Select Member</label>
                            <select name="member_id" class="form-control" required>
                                <option value="">Select Member</option>
                                @foreach ($members as $member)
                                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" name="book_id" value="{{ $book->id }}">
                        <div class="form-group">
                            <label for="borrowed_at">Borrowed Date</label>
                            <input type="date" id="borrowed_at" name="borrowed_at" class="form-control"
                                value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="due_date">Due Date</label>
                            <input type="date" id="due_date" name="due_date" class="form-control"
                                value="{{ now()->addDays(config('library.default_borrow_days'))->format('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Confirm Borrow</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card table-card">
        <div class="card-header">
            <h3 class="card-title mb-0">Borrowing History</h3>
            <span class="badge badge-warning">{{ $book->borrowings->count() }}</span>
        </div>
        <div class="card-body">
            @if ($book->borrowings->isEmpty())
                <x-empty-state icon="fas fa-history" message="No borrowings for this book yet." />
            @else
                <div class="table-responsive-stack">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Member</th>
                                <th>Borrowed</th>
                                <th>Due</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($book->borrowings as $borrowing)
                                <tr>
                                    <td>
                                        <a href="{{ route('members.show', $borrowing->member_id) }}">
                                            {{ $borrowing->member->name }}
                                        </a>
                                    </td>
                                    <td>{{ $borrowing->borrowed_at?->format('Y-m-d') }}</td>
                                    <td>{{ $borrowing->due_date?->format('Y-m-d') }}</td>
                                    <td><x-status-badge :status="$borrowing->status" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="card table-card">
        <div class="card-header">
            <h3 class="card-title mb-0">Reviews</h3>
            <span class="badge badge-warning">{{ $book->reviews->count() }}</span>
        </div>
        <div class="card-body">
            @can('create', App\Models\Review::class)
                <form action="{{ route('reviews.store') }}" method="POST" class="mb-4 border-bottom pb-3">
                    @csrf
                    <input type="hidden" name="book_id" value="{{ $book->id }}">
                    <div class="row">
                        <div class="col-md-4">
                            <label>Member</label>
                            <select name="member_id" class="form-control" required>
                                <option value="">Select member</option>
                                @foreach ($members as $member)
                                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Rating</label>
                            <select name="rating" class="form-control" required>
                                @for ($i = 5; $i >= 1; $i--)
                                    <option value="{{ $i }}">{{ $i }} / 5</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Comment</label>
                            <input type="text" name="comment" class="form-control" placeholder="Optional comment">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary btn-block">Add Review</button>
                        </div>
                    </div>
                </form>
            @endcan

            @if ($book->reviews->isEmpty())
                <x-empty-state icon="fas fa-star" message="No reviews yet." />
            @else
                <div class="table-responsive-stack">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Member</th>
                                <th>Rating</th>
                                <th>Comment</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($book->reviews as $review)
                                <tr>
                                    <td>{{ $review->member->name }}</td>
                                    <td>{{ $review->rating }}/5</td>
                                    <td>{{ $review->comment ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
