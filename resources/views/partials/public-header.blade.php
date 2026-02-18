<!-- Navigation -->
<style>
.nav-item.dropdown .dropdown-menu {
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    border: 1px solid #e5e7eb;
    padding: 0.5rem 0;
    margin-top: 0.5rem;
}
.nav-item.dropdown .dropdown-item {
    padding: 0.6rem 1.25rem;
    font-size: 0.9rem;
    color: #374151;
    transition: all 0.2s ease;
}
.nav-item.dropdown .dropdown-item:hover {
    background: #f3f4f6;
    color: #111827;
}
.nav-item.dropdown .dropdown-item i {
    width: 20px;
    margin-right: 0.5rem;
    color: #6b7280;
}
.nav-item.dropdown .dropdown-header {
    padding: 0.5rem 1.25rem;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6b7280;
    font-weight: 600;
}
.nav-actions .dropdown-menu {
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    border: 1px solid #e5e7eb;
    padding: 0.5rem 0;
    margin-top: 0.5rem;
    max-height: 70vh;
    overflow-y: auto;
    min-width: 250px;
}
.nav-actions .dropdown-item {
    padding: 0.6rem 1.25rem;
    font-size: 0.9rem;
    color: #374151;
    transition: all 0.2s ease;
}
.nav-actions .dropdown-item:hover {
    background: #f3f4f6;
    color: #111827;
}
.nav-actions .dropdown-item i {
    width: 20px;
    margin-right: 0.5rem;
    color: #6b7280;
}
.nav-actions .dropdown-header {
    padding: 0.5rem 1.25rem;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6b7280;
    font-weight: 600;
}
</style>
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
                @php
                    $user = auth()->user();
                    $isPureFaculty = $user->hasRole('Faculty') && !$user->isAdmin && !$user->isResearchCoordinator() && !$user->isDean();
                @endphp
                <li class="nav-item {{ request()->routeIs('submit.*') ? 'active' : '' }}">
                    <a href="{{ route('submit.index') }}" class="nav-link">
                        <i class="fas fa-plus-circle"></i> Submit
                    </a>
                </li>
                <li class="nav-item">
                    @if($isPureFaculty)
                        <a href="{{ route('faculty-members.show', $user->id) }}" class="nav-link">
                            <i class="fas fa-user-circle"></i> My Profile
                        </a>
                    @else
                        <a href="{{ route('admin.home') }}" class="nav-link">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    @endif
                </li>
                <li class="nav-item">
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="nav-link" style="background: none; border: none; cursor: pointer; font-family: inherit; font-size: inherit; color: inherit;">
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
                <button class="btn-submit" onclick="window.location.href='{{ route('submit.index') }}'">
                    <i class="fas fa-paper-plane"></i> Submit
                </button>
            @else
                <button class="btn-submit" onclick="window.location.href='{{ route('register') }}'">
                    <i class="fas fa-paper-plane"></i> Submit
                </button>
            @endauth
        </div>
    </div>
</nav>
