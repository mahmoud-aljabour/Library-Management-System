@extends('layout.app')

@push('style')
    <link rel="stylesheet" href="{{ asset('/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/dist/css/adminlte.min.css') }}">
@endpush




@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">

            <div class="card-header">
                @can('create', App\Models\Book::class)
                    <a class="btn btn-primary" href="{{ route('books.create') }}">Create new Book</a>
                @endcan
            </div>


            <div class="card-body">
                <form action="{{ URL::current() }}" method="get">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <input type="text" name="title" value="{{ request('title') }}" class="form-control"
                                placeholder="Search by title">
                        </div>
                        <div class="col-sm-12 col-md-5">
                            <select name="status" class="form-control">
                                <option value="">All Statuses</option>

                                <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>
                                    Available</option>

                                <option value="borrowed" {{ request('status') == 'borrowed' ? 'selected' : '' }}>
                                    Borrowed</option>

                                <option value="reserved" {{ request('status') == 'reserved' ? 'selected' : '' }}>
                                    Reserved</option>

                                <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>
                                    Archived </option>

                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary  md-2">Filter</button>
                    </div>
                </form>
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Book Title</th>
                            <th>Author</th>
                            <th>Status</th>
                            <th>Available Copies (total copy)</th>
                            <th colspan="2">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($books as $book)
                            <tr>
                                <td><a href="{{ route('books.show', $book->id) }}">{{ $book->title }}</a></td>
                                <td>{{ $book->author->name }}</td>
                                <td>{{ $book->status }}</td>
                                <td>
                                    @if ($book->available_copies > 0)
                                        <span class="badge bg-info"> {{ $book->available_copies }} /
                                            {{ $book->total_copies }}</span>
                                    @else
                                        <span class="badge bg-secondary"> 0 / {{ $book->total_copies }}</span>
                                    @endif
                                </td>

                                <td>
                                    @can('update', $book)
                                        <a href="{{ route('books.edit', $book->id) }}" class="btn btn-secondary">Edit</a>
                                    @endcan
                                </td>
                                <td>
                                    @can('delete', $book)
                                        <form action="{{ route('books.destroy', $book->id) }}" method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this book?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <p> Total Records: {{ $books->total() }}</p>
                {{ $books->withQueryString()->links() }}
            </div>

        </div>

    </div>
@endsection

@push('scripts')
    <script src="{{ asset('/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('/plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('/plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('/plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('/dist/js/adminlte.min.js') }}"></script>
    <script src="{{ asset('/dist/js/demo.js') }}"></script>
    <script>
        $(function() {
            $("#example1").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

        });
    </script>
@endpush
