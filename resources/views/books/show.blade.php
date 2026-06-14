@extends('layout.app')
@push('style')
    <link rel="stylesheet" href="https://cdnjs.com/libraries/bootstrap-modal">
@endpush
@section('content')
    <div class="row">
        <div class="card card-info col-md-8 ml-2">
            <div class="card-header">
                <h3 class="card-title">Book Info</h3>

            </div>

            <div class="card-body">
                <div>
                    <p><b>Book Title :</b> {{ $book->title }} </p>
                    <p><b> Edition :</b> {{ $book->edition }}</p>
                    <p><b> Describtion :</b> {{ $book->description }}</p>
                    <p><b> Language :</b> {{ $book->language }}</p>
                    <p><b> Available Copies :</b> {{ $book->available_copies }} / {{ $book->total_copies }}</p>
                </div>
                <div class="float-right">
                    @can('create', App\Models\Borrowing::class)
                        @if ($book->available_copies > 0)
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#borrowModal">
                            Borrow this book
                        </button>
                    @else
                        <span class="badge badge-secondary">No copies available</span>
                    @endif
                    @endcan

                    <div class="modal fade" id="borrowModal" tabindex="-1" role="dialog"
                        aria-labelledby="borrowModalLabel" aria-hidden="true">
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
                                        <label>Select Member</label>
                                        <select name="member_id" class="form-control" required>
                                            <option value="">Select Member</option>
                                            @foreach ($members as $member)
                                                <option value="{{ $member->id }}">{{ $member->name }}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="book_id" value="{{ $book->id }}">
                                        <div class="form-group mt-3">
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
                </div>
            </div>

        </div>

        <div class="card card-info col-md-3 ml-4">
            <div class="card-header">
                <h3 class="card-title">Author Info</h3>
            </div>
            <div class="card-body">
                <div>
                    <p><b>Author Name :</b>
                        <a href="{{ route('authors.show', $book->author) }}">{{ $book->author->name }}</a>
                    </p>
                    <p><b>Bio :</b> {{ $book->author->bio }}</p>
                    <p><b>Nationality :</b> {{ $book->author->nationality }}</p>
                </div>
            </div>
        </div>

    </div>
    <div class="card card-primary  ">
        <div class="card-header">
            <h3 class="card-title">Borrowing History</h3>
            <span class="badge bg-warning float-right">{{ $book->borrowings->count() }}</span>


        </div>
        <div class="card-body">
            <div>
                {{-- <span class=""> <b> Count : </b>{{ $book->borrowings->count() }}</span> --}}

                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Member Name</th>
                            <th>Borrowed At</th>
                            <th>Due Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($book->borrowings as $borrowing)
                            <tr>
                                <td>
                                    <a href="{{ route('members.show', $borrowing->member_id) }}">
                                        {{ $borrowing->member->name }}
                                    </a>
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
                                <td colspan="4" class="text-center">No borrowings.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>
    </div>
    <div class="card card-secondary">
        <div class="card-header d-flex justify-content-between align-items-center">
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

            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Member Name</th>
                        <th>Rating</th>
                        <th>Comment</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($book->reviews as $review)
                        <tr>
                            <td>{{ $review->member->name }}</td>
                            <td>{{ $review->rating }}/5</td>
                            <td>{{ $review->comment ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">No reviews yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
