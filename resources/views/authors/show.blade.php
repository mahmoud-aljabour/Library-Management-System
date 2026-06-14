@extends('layout.app')

@section('content')
    <div class="card col-md-8">
        <div class="card-header"><h3 class="card-title">{{ $author->name }}</h3></div>
        <div class="card-body">
            <p><strong>Bio:</strong> {{ $author->bio ?? 'N/A' }}</p>
            <p><strong>Birth Date:</strong> {{ $author->birth_date?->format('Y-m-d') ?? 'N/A' }}</p>
            <p><strong>Nationality:</strong> {{ $author->nationality ?? 'N/A' }}</p>
            <p><strong>Books:</strong> {{ $author->books->count() }}</p>
            @if ($author->books->isNotEmpty())
                <ul>
                    @foreach ($author->books as $book)
                        <li><a href="{{ route('books.show', $book) }}">{{ $book->title }}</a></li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@endsection
