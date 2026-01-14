<!-- Navigation -->
<nav class="navbar">
    <div class="nav-container">
        <a href="{{ route('welcome') }}" class="logo">
            <div class="logo-icon">
                <i class="fas fa-atom"></i>
            </div>
            <div class="logo-text">
                <span class="logo-main">Research</span>
                <span class="logo-sub">Portal</span>
            </div>
        </a>
        <button class="menu-toggle" aria-label="Toggle navigation">
            <span class="menu-icon"></span>
        </button>
        <ul class="nav-menu">
            <li class="nav-item {{ request()->routeIs('welcome') ? 'active' : '' }}">
                <a href="{{ route('welcome') }}" class="nav-link">
                    <i class="fas fa-home"></i> Home
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('publications.*') ? 'active' : '' }}">
                <a href="{{ route('publications.index') }}" class="nav-link">
                    <i class="fas fa-book-open"></i> Publications
                </a>
            </li>
            @auth
                @if(auth()->user()->hasRole('Student'))
                    <li class="nav-item">
                        <a href="{{ route('publications.create') }}" class="nav-link">
                            <i class="fas fa-plus-circle"></i> Submit Paper
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{ route('admin.home') }}" class="nav-link">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                @endif
                <li class="nav-item">
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="nav-link" style="background: none; border: none; cursor: pointer; padding: 0;">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                </li>
            @else
                <li class="nav-item {{ request()->routeIs('login') ? 'active' : '' }}">
                    <a href="{{ route('login') }}" class="nav-link">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('register') ? 'active' : '' }}">
                    <a href="{{ route('register') }}" class="nav-link">
                        <i class="fas fa-user-plus"></i> Register
                    </a>
                </li>
            @endauth
            <li class="nav-item">
                <a href="#contact" class="nav-link">
                    <i class="fas fa-envelope"></i> Contact
                </a>
            </li>
        </ul>
        <div class="nav-actions">
            @auth
                @if(auth()->user()->hasRole('Student'))
                    <button class="btn-submit" onclick="window.location.href='{{ route('publications.create') }}'">
                        <i class="fas fa-paper-plane"></i> Submit Paper
                    </button>
                @endif
            @else
                <button class="btn-submit" onclick="window.location.href='{{ route('register') }}'">
                    <i class="fas fa-paper-plane"></i> Submit Paper
                </button>
            @endauth
        </div>
    </div>
</nav>
