@extends('layout.app')

@section('content')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Edit Book</h3>
        </div>

        <form action="{{ route('books.update', $book) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" name="title" id="title"
                                class="form-control @error('title') is-invalid @enderror"
                                placeholder="Enter title" value="{{ old('title', $book->title) }}" required>
                            @error('title')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="isbn">ISBN</label>
                            <input type="text" name="isbn" id="isbn"
                                class="form-control @error('isbn') is-invalid @enderror"
                                placeholder="13-digit ISBN" value="{{ old('isbn', $book->isbn) }}">
                            @error('isbn')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="publish_date">Publish Date</label>
                            <input type="date" name="publish_date" id="publish_date"
                                class="form-control @error('publish_date') is-invalid @enderror"
                                value="{{ old('publish_date', $book->publish_date?->format('Y-m-d')) }}">
                            @error('publish_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="page_count">Page Count</label>
                            <input type="number" name="page_count" id="page_count"
                                class="form-control @error('page_count') is-invalid @enderror"
                                placeholder="Page count" value="{{ old('page_count', $book->page_count) }}" min="1">
                            @error('page_count')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="edition">Edition</label>
                            <input type="text" name="edition" id="edition"
                                class="form-control @error('edition') is-invalid @enderror"
                                placeholder="Edition" value="{{ old('edition', $book->edition) }}">
                            @error('edition')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="publisher_id">Publisher</label>
                            <select name="publisher_id" id="publisher_id"
                                class="form-control @error('publisher_id') is-invalid @enderror">
                                <option value="">Select publisher</option>
                                @foreach ($publishers as $publisher)
                                    <option value="{{ $publisher->id }}"
                                        @selected(old('publisher_id', $book->publisher_id) == $publisher->id)>
                                        {{ $publisher->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('publisher_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="author_id">Author</label>
                            <select name="author_id" id="author_id"
                                class="form-control @error('author_id') is-invalid @enderror" required>
                                <option value="">Select author</option>
                                @foreach ($authors as $author)
                                    <option value="{{ $author->id }}"
                                        @selected(old('author_id', $book->author_id) == $author->id)>
                                        {{ $author->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('author_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status"
                                class="form-control @error('status') is-invalid @enderror">
                                <option value="available" @selected(old('status', $book->status) === 'available')>Available</option>
                                <option value="borrowed" @selected(old('status', $book->status) === 'borrowed')>Borrowed</option>
                                <option value="reserved" @selected(old('status', $book->status) === 'reserved')>Reserved</option>
                                <option value="archived" @selected(old('status', $book->status) === 'archived')>Archived</option>
                            </select>
                            @error('status')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description"
                                class="form-control @error('description') is-invalid @enderror"
                                rows="3" placeholder="Book description">{{ old('description', $book->description) }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="category_ids">Categories</label>
                            <select name="category_ids[]" id="category_ids"
                                class="form-control @error('category_ids') is-invalid @enderror" multiple size="5">
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        @selected(
                                            collect(old('category_ids', $book->categories->pluck('id')))->contains($category->id)
                                        )>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Hold Ctrl (or Cmd) to select multiple.</small>
                            @error('category_ids')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Update Book
                </button>
                <a href="{{ route('books.show', $book) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
