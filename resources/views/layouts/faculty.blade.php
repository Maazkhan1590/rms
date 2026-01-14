@extends('layouts.base')

@section('title', 'Faculty Portal - RMS')

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
@endsection

@section('content')
<div class="faculty-wrapper">
    <!-- Faculty Navigation -->
    <nav class="faculty-navbar">
        <div class="navbar-container">
            <div class="navbar-brand">
                <h1>Research Portal</h1>
                <p>Manage your research and contributions</p>
            </div>
            <div class="navbar-right">
                <!-- Notifications Bell -->
                <div class="navbar-notifications">
                    <button class="notifications-btn" id="facultyNotificationsBell" title="Notifications">
                        <i class="bi bi-bell"></i>
                        <span class="notification-badge">3</span>
                    </button>
                    <div class="notifications-panel" id="facultyNotificationsPanel">
                        <div class="notifications-header">
                            <h4>Notifications</h4>
                            <button class="btn-link" id="markAllRead">Mark all as read</button>
                        </div>
                        <div class="notifications-list">
                            <div class="notification-item unread">
                                <div class="notification-icon">✓</div>
                                <div class="notification-body">
                                    <p class="notification-title">Publication Approved</p>
                                    <p class="notification-time">2 hours ago</p>
                                </div>
                            </div>
                            <div class="notification-item unread">
                                <div class="notification-icon">⏱</div>
                                <div class="notification-body">
                                    <p class="notification-title">Grant Application Updated</p>
                                    <p class="notification-time">5 hours ago</p>
                                </div>
                            </div>
                            <div class="notification-item unread">
                                <div class="notification-icon">💬</div>
                                <div class="notification-body">
                                    <p class="notification-title">New Comment on Publication</p>
                                    <p class="notification-time">1 day ago</p>
                                </div>
                            </div>
                        </div>
                        <div class="notifications-footer">
                            <a href="#" class="btn-link">View All Notifications</a>
                        </div>
                    </div>
                </div>

                <button class="navbar-menu-btn" id="facultyMenuBtn">
                    <span></span><span></span><span></span>
                </button>
                <div class="navbar-user-menu">
                    <div class="user-info">
                        <div class="user-avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
                        <div>
                            <p>{{ auth()->user()->name }}</p>
                            <small>Faculty Member</small>
                        </div>
                    </div>
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="faculty-content">
        <!-- Sidebar Menu -->
        <aside class="faculty-sidebar" id="facultySidebar">
            <ul class="faculty-menu">
                <li>
                    <a href="#dashboard" class="menu-item active">
                        <span class="icon">📊</span>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="#my-publications" class="menu-item">
                        <span class="icon">📚</span>
                        <span>My Publications</span>
                    </a>
                </li>
                <li>
                    <a href="#my-grants" class="menu-item">
                        <span class="icon">💰</span>
                        <span>My Grants</span>
                    </a>
                </li>
                <li>
                    <a href="#my-awards" class="menu-item">
                        <span class="icon">🏆</span>
                        <span>Awards</span>
                    </a>
                </li>
                <li>
                    <a href="#profile" class="menu-item">
                        <span class="icon">👤</span>
                        <span>My Profile</span>
                    </a>
                </li>
                <li>
                    <a href="#submissions" class="menu-item">
                        <span class="icon">📤</span>
                        <span>Submissions</span>
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Page Content -->
        <main class="faculty-main">
            @if(session('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            @endif

            @yield('faculty-content')
        </main>
    </div>
</div>

<!-- Logout Form -->
<form id="logoutform" action="{{ route('logout') }}" method="POST" style="display: none;">
    {{ csrf_field() }}
</form>

<style>
    body {
        background-color: var(--bg-secondary);
    }

    .faculty-wrapper {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    /* Faculty Navigation */
    .faculty-navbar {
        background: var(--bg-primary);
        border-bottom: 1px solid var(--color-gray-200);
        box-shadow: var(--shadow-sm);
        position: sticky;
        top: 0;
        z-index: 100;
    }

    .navbar-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: var(--spacing-lg);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: var(--spacing-lg);
    }

    .navbar-brand h1 {
        margin: 0;
        font-size: var(--font-size-2xl);
        color: var(--color-primary);
    }

    .navbar-brand p {
        margin: var(--spacing-xs) 0 0 0;
        font-size: var(--font-size-sm);
        color: var(--text-secondary);
    }

    .navbar-right {
        display: flex;
        align-items: center;
        gap: var(--spacing-lg);
    }

    .navbar-menu-btn {
        display: none;
        background: none;
        border: none;
        cursor: pointer;
        font-size: var(--font-size-lg);
    }

    .navbar-menu-btn span {
        display: block;
        width: 24px;
        height: 2px;
        background: var(--text-primary);
        margin: 4px 0;
        transition: var(--transition-fast);
    }

    .navbar-user-menu {
        display: flex;
        align-items: center;
        gap: var(--spacing-md);
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: var(--spacing-md);
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        background: var(--color-primary);
        color: white;
        border-radius: var(--radius-full);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: var(--font-weight-bold);
        font-size: var(--font-size-lg);
    }

    .user-info p {
        margin: 0;
        font-weight: var(--font-weight-medium);
        color: var(--text-primary);
    }

    .user-info small {
        display: block;
        color: var(--text-secondary);
        font-size: var(--font-size-sm);
    }

    .navbar-user-menu a {
        color: var(--color-primary);
        text-decoration: none;
        font-size: var(--font-size-sm);
        padding: var(--spacing-sm) var(--spacing-md);
        border-radius: var(--radius-lg);
        transition: var(--transition-fast);
    }

    .navbar-user-menu a:hover {
        background-color: var(--bg-secondary);
    }

    /* Faculty Content */
    .faculty-content {
        display: flex;
        flex: 1;
        max-width: 1400px;
        width: 100%;
        margin: 0 auto;
        gap: var(--spacing-lg);
        padding: var(--spacing-lg);
    }

    /* Faculty Sidebar */
    .faculty-sidebar {
        width: 200px;
        background: var(--bg-primary);
        border-radius: var(--radius-lg);
        padding: var(--spacing-lg);
        box-shadow: var(--shadow-sm);
        height: fit-content;
        position: sticky;
        top: 100px;
    }

    .faculty-menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .navbar-right {
        display: flex;
        align-items: center;
        gap: var(--spacing-lg);
    }

    /* Notifications */
    .navbar-notifications {
        position: relative;
    }

    .notifications-btn {
        position: relative;
        background: none;
        border: none;
        cursor: pointer;
        font-size: var(--font-size-xl);
        color: var(--text-secondary);
        transition: color var(--transition-fast);
        padding: 0;
    }

    .notifications-btn:hover {
        color: var(--color-primary);
    }

    .notification-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background: var(--color-danger);
        color: white;
        border-radius: var(--radius-full);
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: var(--font-size-xs);
        font-weight: var(--font-weight-bold);
    }

    .notifications-panel {
        position: absolute;
        right: 0;
        top: 100%;
        margin-top: var(--spacing-sm);
        width: 360px;
        background: var(--bg-primary);
        border: 1px solid var(--color-gray-200);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-lg);
        display: none;
        z-index: 1000;
        flex-direction: column;
        max-height: 400px;
    }

    .notifications-panel.show {
        display: flex;
    }

    .notifications-header {
        padding: var(--spacing-md) var(--spacing-lg);
        border-bottom: 1px solid var(--color-gray-200);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .notifications-header h4 {
        margin: 0;
        font-size: var(--font-size-base);
        color: var(--text-primary);
    }

    .notifications-list {
        flex: 1;
        overflow-y: auto;
    }

    .notification-item {
        display: flex;
        gap: var(--spacing-md);
        padding: var(--spacing-md) var(--spacing-lg);
        border-bottom: 1px solid var(--color-gray-100);
        cursor: pointer;
        transition: background-color var(--transition-fast);
    }

    .notification-item:hover {
        background-color: var(--bg-secondary);
    }

    .notification-item.unread {
        background-color: var(--color-primary-50);
    }

    .notification-icon {
        width: 36px;
        height: 36px;
        border-radius: var(--radius-lg);
        background: var(--bg-secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: var(--font-size-lg);
    }

    .notification-body {
        flex: 1;
    }

    .notification-title {
        margin: 0 0 var(--spacing-xs) 0;
        font-size: var(--font-size-sm);
        font-weight: var(--font-weight-semibold);
        color: var(--text-primary);
    }

    .notification-time {
        margin: 0;
        font-size: var(--font-size-xs);
        color: var(--text-light);
    }

    .notifications-footer {
        padding: var(--spacing-md) var(--spacing-lg);
        border-top: 1px solid var(--color-gray-200);
        text-align: center;
    }

    .btn-link {
        background: none;
        border: none;
        color: var(--color-primary);
        cursor: pointer;
        font-size: var(--font-size-sm);
        text-decoration: underline;
        padding: 0;
    }

    .btn-link:hover {
        color: var(--color-primary-600);
    }

    .menu-item {
        display: flex;
        align-items: center;
        gap: var(--spacing-md);
        padding: var(--spacing-md) var(--spacing-lg);
        color: var(--text-secondary);
        text-decoration: none;
        border-radius: var(--radius-lg);
        transition: var(--transition-fast);
        cursor: pointer;
    }

    .menu-item:hover {
        background-color: var(--bg-secondary);
        color: var(--color-primary);
    }

    .menu-item.active {
        background-color: var(--color-primary-50);
        color: var(--color-primary);
        font-weight: var(--font-weight-semibold);
    }

    .menu-item .icon {
        font-size: var(--font-size-lg);
    }

    /* Faculty Main */
    .faculty-main {
        flex: 1;
    }

    .faculty-main .alert {
        margin-bottom: var(--spacing-lg);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .navbar-menu-btn {
            display: block;
        }

        .faculty-content {
            flex-direction: column;
            padding: var(--spacing-md);
            gap: var(--spacing-md);
        }

        .faculty-sidebar {
            width: 100%;
            position: static;
            max-height: 0;
            overflow: hidden;
            padding: 0;
            border-radius: 0;
            box-shadow: none;
            background: var(--bg-secondary);
            transition: max-height var(--transition-base);
        }

        .faculty-sidebar.show {
            max-height: 400px;
            padding: var(--spacing-lg);
            border-radius: var(--radius-lg);
            background: var(--bg-primary);
            box-shadow: var(--shadow-sm);
        }

        .navbar-container {
            padding: var(--spacing-md);
        }

        .navbar-brand h1 {
            font-size: var(--font-size-lg);
        }

        .navbar-brand p {
            display: none;
        }

        .user-info {
            gap: var(--spacing-sm);
        }

        .user-info p {
            display: none;
        }
    }

    @media (max-width: 480px) {
        .navbar-container {
            flex-wrap: wrap;
        }

        .navbar-brand {
            width: 100%;
        }

        .faculty-sidebar {
            order: -1;
        }
    }
</style>

<script>
    // Faculty sidebar toggle
    document.getElementById('facultyMenuBtn')?.addEventListener('click', function() {
        document.getElementById('facultySidebar').classList.toggle('show');
    });

    // Menu item active state
    document.querySelectorAll('.menu-item').forEach(item => {
        item.addEventListener('click', function() {
            document.querySelectorAll('.menu-item').forEach(i => i.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Notifications Panel Toggle
    const notificationsBell = document.getElementById('facultyNotificationsBell');
    const notificationsPanel = document.getElementById('facultyNotificationsPanel');

    if (notificationsBell) {
        notificationsBell.addEventListener('click', function(e) {
            e.stopPropagation();
            notificationsPanel.classList.toggle('show');
        });
    }

    // Close notifications when clicking outside
    document.addEventListener('click', function(e) {
        if (notificationsBell && !notificationsBell.contains(e.target) && !notificationsPanel?.contains(e.target)) {
            notificationsPanel?.classList.remove('show');
        }
    });

    // Mark all notifications as read
    const markAllReadBtn = document.getElementById('markAllRead');
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('.notification-item.unread').forEach(item => {
                item.classList.remove('unread');
            });
        });
    }
