@extends('layout.app')

@section('content')
    <div class="card col-md-8">
        <div class="card-header"><h3 class="card-title">{{ $category->name }}</h3></div>
        <div class="card-body">
            <p><strong>Description:</strong> {{ $category->description ?? 'N/A' }}</p>
            @if ($category->books->isNotEmpty())
                <ul>@foreach ($category->books as $book)
                    <li><a href="{{ route('books.show', $book) }}">{{ $book->title }}</a> — {{ $book->author->name }}</li>
                @endforeach</ul>
            @endif
        </div>
    </div>
@endsection
