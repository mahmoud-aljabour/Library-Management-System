@extends('layout.app')

@section('content')
    <div class="card col-md-8">
        <div class="card-header"><h3 class="card-title">{{ $publisher->name }}</h3></div>
        <div class="card-body">
            <p><strong>Address:</strong> {{ $publisher->address ?? 'N/A' }}</p>
            <p><strong>Website:</strong>
                @if ($publisher->website)
                    <a href="{{ $publisher->website }}" target="_blank">{{ $publisher->website }}</a>
                @else N/A @endif
            </p>
            @if ($publisher->books->isNotEmpty())
                <ul>@foreach ($publisher->books as $book)
                    <li><a href="{{ route('books.show', $book) }}">{{ $book->title }}</a></li>
                @endforeach</ul>
            @endif
        </div>
    </div>
@endsection
