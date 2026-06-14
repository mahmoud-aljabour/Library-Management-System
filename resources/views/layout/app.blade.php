<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        @hasSection('title')
            @yield('title') - {{ config('app.name', 'Library System') }}
        @else
            {{ $pageTitle }} - {{ config('app.name', 'Library System') }}
        @endif
    </title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="{{ asset('/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/library.css') }}">
    @stack('style')
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="{{ route('dashboard') }}" class="nav-link">Dashboard</a>
                </li>
            </ul>

            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="fas fa-user-circle"></i>
                        {{ auth()->user()->name }}
                        <span class="badge badge-{{ auth()->user()->isAdmin() ? 'danger' : 'info' }} ml-1">
                            {{ ucfirst(auth()->user()->role->value) }}
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <span class="dropdown-item-text text-muted">{{ auth()->user()->email }}</span>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </button>
                        </form>
                    </div>
                </li>
            </ul>
        </nav>

        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="{{ route('dashboard') }}" class="brand-link">
                <span class="brand-image img-circle elevation-3">
                    <i class="fas fa-book"></i>
                </span>
                <span class="brand-text font-weight-light">{{ config('app.name', 'Library System') }}</span>
            </a>

            <div class="sidebar">
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <div class="user-avatar-initials elevation-2">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    </div>
                    <div class="info">
                        <a href="#" class="d-block">{{ auth()->user()->name }}</a>
                        <small class="text-muted">{{ ucfirst(auth()->user()->role->value) }}</small>
                    </div>
                </div>

                @include('layout.nav')
            </div>
        </aside>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">
                                @hasSection('title')
                                    @yield('title')
                                @else
                                    {{ $pageTitle }}
                                @endif
                            </h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                @hasSection('breadcrumbs')
                                    @yield('breadcrumbs')
                                @else
                                    @foreach ($breadcrumbs as $crumb)
                                        @if ($crumb['url'])
                                            <li class="breadcrumb-item">
                                                <a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a>
                                            </li>
                                        @else
                                            <li class="breadcrumb-item active">{{ $crumb['label'] }}</li>
                                        @endif
                                    @endforeach
                                @endif
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content">
                <div class="container-fluid">
                    @include('layout.partials.alerts')
                    @yield('content')
                </div>
            </div>
        </div>

        <footer class="main-footer">
            <div class="float-right d-none d-sm-inline">
                Laravel {{ app()->version() }}
            </div>
            <strong>&copy; {{ date('Y') }} {{ config('app.name', 'Library System') }}.</strong> All rights reserved.
        </footer>
    </div>

    <script src="{{ asset('/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('/dist/js/adminlte.min.js') }}"></script>
    @stack('scripts')
</body>
</html>
