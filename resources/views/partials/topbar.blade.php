<div class="container flex" style="justify-content: space-between; align-items: center; gap: var(--spacing-md);">
    <div class="flex" style="align-items:center; gap: var(--spacing-md);">
        <button id="btnSidebar" class="btn btn-outline" aria-label="Toggle sidebar" style="display:inline-flex;">
            <i class="bi bi-list"></i>
        </button>
        <a href="{{ url('/') }}" class="brand text-primary" style="display:flex; align-items:center; gap: .5rem; text-decoration:none;">
            <i class="bi bi-mortarboard" aria-hidden="true"></i>
            <span>{{ config('app.name', 'RMS') }}</span>
        </a>
    </div>

    <div class="search" style="flex:1; max-width:520px;">
        <div class="form-floating">
            <input type="search" id="global-search" class="form-control" placeholder="Search..." aria-label="Search">
            <label for="global-search">Search</label>
        </div>
    </div>

    <div class="actions">
        <button class="btn btn-outline" id="btnDarkMode" aria-label="Toggle dark mode" title="Toggle dark mode">
            <i class="bi bi-moon-stars" id="darkIcon"></i>
        </button>
        <button class="btn btn-outline" aria-label="Notifications">
            <i class="bi bi-bell"></i>
        </button>

        <div class="dropdown" style="position: relative;">
            <button class="btn btn-secondary" id="userMenuBtn" aria-haspopup="true" aria-expanded="false">
                <img src="https://www.gravatar.com/avatar/?d=mp&s=28" alt="Avatar" class="rounded-full" style="width:28px;height:28px;">
                <span class="hidden md:inline">{{ auth()->user()->name ?? 'User' }}</span>
                <i class="bi bi-caret-down-fill"></i>
            </button>
            <div class="dropdown-menu hidden" id="userMenu" role="menu" aria-labelledby="userMenuBtn" style="position:absolute; right:0; top:115%; background: var(--bg-primary); border:1px solid var(--color-gray-200); border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); min-width:200px;">
                <a href="#" class="dropdown-item" style="display:block; padding: .75rem 1rem; color: var(--text-primary); text-decoration:none;">Profile</a>
                <a href="#" class="dropdown-item" style="display:block; padding: .75rem 1rem; color: var(--text-primary); text-decoration:none;">Settings</a>
                <div style="height:1px; background: var(--color-gray-200);"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item" style="display:block; width:100%; text-align:left; padding: .75rem 1rem; color: var(--text-primary); background:transparent; border:none; cursor:pointer;">Logout</button>
                </form>
            </div>
        </div>
    </div>
</div>
