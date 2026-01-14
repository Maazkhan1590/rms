<nav>
    <ul class="nav">
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.home') || request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <i class="bi bi-house-door"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li class="nav-item">
            <button class="nav-link" data-collapse="research">
                <i class="bi bi-journal-text"></i>
                <span>Research</span>
                <i class="bi bi-chevron-down" style="margin-left:auto;"></i>
            </button>
            <ul class="nav hidden" id="collapse-research" style="padding-left: .5rem;">
                <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-file-earmark-text"></i> Publications</a></li>
                <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-cash-stack"></i> Grants</a></li>
                <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-calendar3"></i> Conferences</a></li>
            </ul>
        </li>

        <li class="nav-item">
            <button class="nav-link" data-collapse="users">
                <i class="bi bi-people"></i>
                <span>Users</span>
                <i class="bi bi-chevron-down" style="margin-left:auto;"></i>
            </button>
            <ul class="nav hidden" id="collapse-users" style="padding-left: .5rem;">
                <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-person-badge"></i> Roles</a></li>
                <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-shield-check"></i> Permissions</a></li>
            </ul>
        </li>

        <li class="nav-item">
            <button class="nav-link" data-collapse="reports">
                <i class="bi bi-graph-up"></i>
                <span>Reports</span>
                <i class="bi bi-chevron-down" style="margin-left:auto;"></i>
            </button>
            <ul class="nav hidden" id="collapse-reports" style="padding-left: .5rem;">
                <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-bar-chart"></i> Summary</a></li>
                <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-pie-chart"></i> Analytics</a></li>
            </ul>
        </li>

        <li class="nav-item">
            <button class="nav-link" data-collapse="settings">
                <i class="bi bi-gear"></i>
                <span>Settings</span>
                <i class="bi bi-chevron-down" style="margin-left:auto;"></i>
            </button>
            <ul class="nav hidden" id="collapse-settings" style="padding-left: .5rem;">
                <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-sliders"></i> System</a></li>
                <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-palette"></i> Appearance</a></li>
            </ul>
        </li>
    </ul>

    <div style="margin-top:auto; padding-top: var(--spacing-lg); color: var(--text-light); font-size: var(--font-size-sm);">
        <div>RMS Admin</div>
        <div>v{{ env('APP_VERSION', '1.0.0') }}</div>
    </div>
</nav>
