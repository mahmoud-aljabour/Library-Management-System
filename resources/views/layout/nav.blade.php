<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-header">MAIN</li>
        <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('books.index') }}" class="nav-link {{ request()->routeIs('books.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-book"></i>
                <p>Books</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('members.index') }}" class="nav-link {{ request()->routeIs('members.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-users"></i>
                <p>Members</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('borrowings.index') }}" class="nav-link {{ request()->routeIs('borrowings.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-exchange-alt"></i>
                <p>Borrowings</p>
            </a>
        </li>

        <li class="nav-header">MANAGEMENT</li>
        <li class="nav-item">
            <a href="{{ route('authors.index') }}" class="nav-link {{ request()->routeIs('authors.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-pen-fancy"></i>
                <p>Authors</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('publishers.index') }}" class="nav-link {{ request()->routeIs('publishers.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-building"></i>
                <p>Publishers</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-tags"></i>
                <p>Categories</p>
            </a>
        </li>
    </ul>
</nav>
