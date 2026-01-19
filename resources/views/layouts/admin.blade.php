
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', trans('panel.site_title'))</title>

    <!-- Custom Admin Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet" />
    <!-- DataTables core + extensions (vanilla theme) -->
    <link href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/select/1.7.0/css/select.dataTables.min.css" rel="stylesheet" />
    <!-- Select2 CSS for multi-select fields in admin forms -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css" rel="stylesheet" />
    @yield('styles')
</head>
<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar Navigation -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h2>RMS</h2>
                <button class="sidebar-toggle" id="sidebarToggle" type="button">
                    <span></span><span></span><span></span>
                </button>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section">
                    <h3 class="nav-section-title">Main</h3>
                    <ul>
                        <li>
                            <a href="{{ route('admin.home') }}" class="nav-link {{ request()->routeIs('admin.home') ? 'active' : '' }}">
                                <span class="nav-icon">📊</span>
                                <span class="nav-label">Dashboard</span>
                            </a>
                        </li>
                    </ul>
                </div>

                @can('user_management_access')
                <div class="nav-section">
                    <h3 class="nav-section-title">User Management</h3>
                    <ul>
                        @can('user_access')
                        <li>
                            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                <span class="nav-icon">👥</span>
                                <span class="nav-label">Users</span>
                            </a>
                        </li>
                        @endcan
                        @can('role_access')
                        <li>
                            <a href="{{ route('admin.roles.index') }}" class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                                <span class="nav-icon">🔐</span>
                                <span class="nav-label">Roles</span>
                            </a>
                        </li>
                        @endcan
                        @can('permission_access')
                        <li>
                            <a href="{{ route('admin.permissions.index') }}" class="nav-link {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}">
                                <span class="nav-icon">🔑</span>
                                <span class="nav-label">Permissions</span>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </div>
                @endcan

                @canany(['publication_access', 'grant_access', 'rtn_access', 'bonus_access'])
                <div class="nav-section">
                    <h3 class="nav-section-title">Research Submissions</h3>
                    <ul>
                        @can('publication_access')
                        <li>
                            <a href="{{ route('admin.publications.index') }}" class="nav-link {{ request()->routeIs('admin.publications.*') ? 'active' : '' }}">
                                <span class="nav-icon">📚</span>
                                <span class="nav-label">Publications</span>
                            </a>
                        </li>
                        @endcan
                        @can('grant_access')
                        <li>
                            <a href="{{ route('admin.grants.index') }}" class="nav-link {{ request()->routeIs('admin.grants.*') ? 'active' : '' }}">
                                <span class="nav-icon">💰</span>
                                <span class="nav-label">Grants</span>
                            </a>
                        </li>
                        @endcan
                        @can('rtn_access')
                        <li>
                            <a href="{{ route('admin.rtn-submissions.index') }}" class="nav-link {{ request()->routeIs('admin.rtn-submissions.*') ? 'active' : '' }}">
                                <span class="nav-icon">📖</span>
                                <span class="nav-label">RTN Submissions</span>
                            </a>
                        </li>
                        @endcan
                        @can('bonus_access')
                        <li>
                            <a href="{{ route('admin.bonus-recognitions.index') }}" class="nav-link {{ request()->routeIs('admin.bonus-recognitions.*') ? 'active' : '' }}">
                                <span class="nav-icon">🏆</span>
                                <span class="nav-label">Bonus Recognition</span>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </div>
                @endcanany

                @canany(['consultancy_access', 'commercialization_access', 'partnership_access', 'conference_access', 'investment_access'])
                <div class="nav-section" style="display: none">
                    <h3 class="nav-section-title">Research Activities</h3>
                    <ul>
                        @can('consultancy_access')
                        <li>
                            <a href="{{ route('admin.consultancies.index') }}" class="nav-link {{ request()->routeIs('admin.consultancies.*') ? 'active' : '' }}">
                                <span class="nav-icon">💼</span>
                                <span class="nav-label">Consultancies & KT</span>
                            </a>
                        </li>
                        @endcan
                        @can('commercialization_access')
                        <li>
                            <a href="{{ route('admin.commercializations.index') }}" class="nav-link {{ request()->routeIs('admin.commercializations.*') ? 'active' : '' }}">
                                <span class="nav-icon">🚀</span>
                                <span class="nav-label">Commercializations</span>
                            </a>
                        </li>
                        @endcan
                        @can('partnership_access')
                        <li>
                            <a href="{{ route('admin.partnerships.index') }}" class="nav-link {{ request()->routeIs('admin.partnerships.*') ? 'active' : '' }}">
                                <span class="nav-icon">🤝</span>
                                <span class="nav-label">Partnerships & MOUs</span>
                            </a>
                        </li>
                        @endcan
                        @can('conference_access')
                        <li>
                            <a href="{{ route('admin.conference-activities.index') }}" class="nav-link {{ request()->routeIs('admin.conference-activities.*') ? 'active' : '' }}">
                                <span class="nav-icon">🎤</span>
                                <span class="nav-label">Conference Activities</span>
                            </a>
                        </li>
                        @endcan
                        @can('investment_access')
                        <li>
                            <a href="{{ route('admin.research-investments.index') }}" class="nav-link {{ request()->routeIs('admin.research-investments.*') ? 'active' : '' }}">
                                <span class="nav-icon">💻</span>
                                <span class="nav-label">Research Investments</span>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </div>
                @endcanany

                @canany(['supervision_access', 'editorial_access', 'student_access', 'internal_funding_access'])
                <div class="nav-section" style="display: none">
                    <h3 class="nav-section-title">Academic Activities</h3>
                    <ul>
                        @can('supervision_access')
                        <li>
                            <a href="{{ route('admin.supervision-exams.index') }}" class="nav-link {{ request()->routeIs('admin.supervision-exams.*') ? 'active' : '' }}">
                                <span class="nav-icon">🎓</span>
                                <span class="nav-label">Supervision & Exams</span>
                            </a>
                        </li>
                        @endcan
                        @can('editorial_access')
                        <li>
                            <a href="{{ route('admin.editorial-appointments.index') }}" class="nav-link {{ request()->routeIs('admin.editorial-appointments.*') ? 'active' : '' }}">
                                <span class="nav-icon">✍️</span>
                                <span class="nav-label">Editorial Appointments</span>
                            </a>
                        </li>
                        @endcan
                        @can('student_access')
                        <li>
                            <a href="{{ route('admin.student-involvements.index') }}" class="nav-link {{ request()->routeIs('admin.student-involvements.*') ? 'active' : '' }}">
                                <span class="nav-icon">👨‍🎓</span>
                                <span class="nav-label">Student Involvements</span>
                            </a>
                        </li>
                        @endcan
                        @can('internal_funding_access')
                        <li>
                            <a href="{{ route('admin.internal-fundings.index') }}" class="nav-link {{ request()->routeIs('admin.internal-fundings.*') ? 'active' : '' }}">
                                <span class="nav-icon">💵</span>
                                <span class="nav-label">Internal Fundings</span>
                            </a>
                        </li>
                        @endcan
                        @can('block_funding_access')
                        <li>
                            <a href="{{ route('admin.block-fundings.index') }}" class="nav-link {{ request()->routeIs('admin.block-fundings.*') ? 'active' : '' }}">
                                <span class="nav-icon">📦</span>
                                <span class="nav-label">Block Fundings</span>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </div>
                @endcanany

                @can('sdg_access')
                <div class="nav-section" style="display: none">
                    <h3 class="nav-section-title">SDG & Impact</h3>
                    <ul>
                        <li>
                            <a href="{{ route('admin.sdg-contributions.index') }}" class="nav-link {{ request()->routeIs('admin.sdg-contributions.*') ? 'active' : '' }}">
                                <span class="nav-icon">🌍</span>
                                <span class="nav-label">SDG Contributions</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.sdg-mappings.index') }}" class="nav-link {{ request()->routeIs('admin.sdg-mappings.*') ? 'active' : '' }}">
                                <span class="nav-icon">🗺️</span>
                                <span class="nav-label">SDG Mappings</span>
                            </a>
                        </li>
                    </ul>
                </div>
                @endcan

                @can('workflow_access')
                <div class="nav-section">
                    <h3 class="nav-section-title">Workflow Management</h3>
                    <ul>
                        <li>
                            <a href="{{ route('admin.workflow-assignments.index') }}" class="nav-link {{ request()->routeIs('admin.workflow-assignments.index') || request()->routeIs('admin.workflow-assignments.create') || request()->routeIs('admin.workflow-assignments.edit') || request()->routeIs('admin.workflow-assignments.show') ? 'active' : '' }}">
                                <span class="nav-icon">👥</span>
                                <span class="nav-label">Workflow Assignments</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.workflow-assignments.visualization') }}" class="nav-link {{ request()->routeIs('admin.workflow-assignments.visualization') ? 'active' : '' }}">
                                <span class="nav-icon">📊</span>
                                <span class="nav-label">Workflow Diagram</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.workflows.pending') }}" class="nav-link {{ request()->routeIs('admin.workflows.pending') ? 'active' : '' }}">
                                <span class="nav-icon">⏳</span>
                                <span class="nav-label">Pending Approvals</span>
                                @if(isset($pendingWorkflowsCount) && $pendingWorkflowsCount > 0)
                                    <span class="badge badge-warning">{{ $pendingWorkflowsCount }}</span>
                                @endif
                            </a>
                        </li>
                    </ul>
                </div>
                @endcan

                @can('policy_access')
                <div class="nav-section">
                    <h3 class="nav-section-title">Scoring & Policies</h3>
                    <ul>
                        <li>
                            <a href="{{ route('admin.policies.index') }}" class="nav-link {{ request()->routeIs('admin.policies.*') ? 'active' : '' }}">
                                <span class="nav-icon">📊</span>
                                <span class="nav-label">Scoring Policies</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.policy-versions.index') }}" class="nav-link {{ request()->routeIs('admin.policy-versions.*') ? 'active' : '' }}">
                                <span class="nav-icon">📑</span>
                                <span class="nav-label">Policy Versions</span>
                            </a>
                        </li>
                    </ul>
                </div>
                @endcan

                @can('report_access')
                <div class="nav-section" style="display: none">
                    <h3 class="nav-section-title">Reports</h3>
                    <ul>
                        <li>
                            <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                                <span class="nav-icon">📈</span>
                                <span class="nav-label">Generate Reports</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.reports.cv') }}" class="nav-link">
                                <span class="nav-icon">📄</span>
                                <span class="nav-label">CV Reports</span>
                            </a>
                        </li>
                    </ul>
                </div>
                @endcan

                @can('college_access')
                <div class="nav-section">
                    <h3 class="nav-section-title">Organization</h3>
                    <ul>
                        <li>
                            <a href="{{ route('admin.colleges.index') }}" class="nav-link {{ request()->routeIs('admin.colleges.*') ? 'active' : '' }}">
                                <span class="nav-icon">🏛️</span>
                                <span class="nav-label">Colleges</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.departments.index') }}" class="nav-link {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}">
                                <span class="nav-icon">🏢</span>
                                <span class="nav-label">Departments</span>
                            </a>
                        </li>
                    </ul>
                </div>
                @endcan

                @can('audit_access')
                <div class="nav-section">
                    <h3 class="nav-section-title">System</h3>
                    <ul>
                        <li>
                            <a href="{{ route('admin.audit-logs.index') }}" class="nav-link {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}">
                                <span class="nav-icon">📋</span>
                                <span class="nav-label">Audit Logs</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.activity-logs.index') }}" class="nav-link {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}">
                                <span class="nav-icon">📝</span>
                                <span class="nav-label">Activity Logs</span>
                            </a>
                        </li>
                    </ul>
                </div>
                @endcan

                <!-- Faculty Portal Links -->
                @if(auth()->user()->hasRole('faculty') || auth()->user()->hasRole('user'))
                <div class="nav-section">
                    <h3 class="nav-section-title">My Research</h3>
                    <ul>
                        <li>
                            <a href="{{ route('faculty.publications.index') }}" class="nav-link {{ request()->routeIs('faculty.publications.index') ? 'active' : '' }}">
                                <span class="nav-icon">📚</span>
                                <span class="nav-label">My Publications</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('faculty.publications.all') }}" class="nav-link {{ request()->routeIs('faculty.publications.all') ? 'active' : '' }}">
                                <span class="nav-icon">📖</span>
                                <span class="nav-label">All Publications</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('faculty.publications.create') }}" class="nav-link {{ request()->routeIs('faculty.publications.create') ? 'active' : '' }}">
                                <span class="nav-icon">➕</span>
                                <span class="nav-label">Submit Publication</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('faculty.grants.index') }}" class="nav-link {{ request()->routeIs('faculty.grants.*') ? 'active' : '' }}">
                                <span class="nav-icon">💰</span>
                                <span class="nav-label">My Grants</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('faculty.rtn-submissions.index') }}" class="nav-link {{ request()->routeIs('faculty.rtn-submissions.*') ? 'active' : '' }}">
                                <span class="nav-icon">📖</span>
                                <span class="nav-label">My RTN</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('faculty.bonus-recognitions.index') }}" class="nav-link {{ request()->routeIs('faculty.bonus-recognitions.*') ? 'active' : '' }}">
                                <span class="nav-icon">🏆</span>
                                <span class="nav-label">My Recognitions</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('faculty.consultancies.index') }}" class="nav-link {{ request()->routeIs('faculty.consultancies.*') ? 'active' : '' }}">
                                <span class="nav-icon">💼</span>
                                <span class="nav-label">My Consultancies</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('faculty.commercializations.index') }}" class="nav-link {{ request()->routeIs('faculty.commercializations.*') ? 'active' : '' }}">
                                <span class="nav-icon">🚀</span>
                                <span class="nav-label">My Commercializations</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('faculty.conference-activities.index') }}" class="nav-link {{ request()->routeIs('faculty.conference-activities.*') ? 'active' : '' }}">
                                <span class="nav-icon">🎤</span>
                                <span class="nav-label">My Conferences</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('faculty.dashboard') }}" class="nav-link {{ request()->routeIs('faculty.dashboard') ? 'active' : '' }}">
                                <span class="nav-icon">📊</span>
                                <span class="nav-label">My Dashboard</span>
                            </a>
                        </li>
                    </ul>
                </div>
                @endif
            </nav>

            <div class="sidebar-footer">
                <p>RMS v1.0</p>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Navigation -->
            <header class="navbar">
                <div class="navbar-container">
                    <button class="navbar-hamburger" id="navbarToggle">
                        <span></span><span></span><span></span>
                    </button>

                    <div class="navbar-brand">
                        <h1>@yield('page-title', trans('panel.site_title'))</h1>
                    </div>

                    <div class="navbar-right">
                        <div class="navbar-item dropdown">
                            <button class="navbar-user" id="userDropdown" type="button">
                                <div class="user-avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
                                <span>{{ auth()->user()->name }}</span>
                            </button>
                            <div class="dropdown-menu" id="userMenu">
                                <a href="{{ route('profile.password.edit') }}" class="dropdown-item">
                                    My Profile
                                </a>
                                <a href="{{ route('profile.password.edit') }}" class="dropdown-item">
                                    Settings
                                </a>
                                <hr class="dropdown-divider">
                                <a href="{{ route('logout') }}" class="dropdown-item"
                                   onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                                    Logout
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Alerts & Messages -->
            <div class="content-alerts">
                @if(session('message'))
                    <div class="alert alert-success" role="alert">
                        {{ session('message') }}
                        <button class="alert-close" type="button">&times;</button>
                    </div>
                @endif

                @if($errors->count() > 0)
                    <div class="alert alert-danger" role="alert">
                        <h5 class="alert-title">Please fix the following errors:</h5>
                        <ul class="list-unstyled mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button class="alert-close" type="button">&times;</button>
                    </div>
                @endif
            </div>

            <!-- Page Content -->
            <div class="page-content">
                @yield('content')
            </div>

            <!-- Footer -->
            <footer class="footer">
                <p>&copy; {{ date('Y') }} Research Management System. All rights reserved.</p>
            </footer>
        </main>
    </div>

    <!-- Logout Form -->
    <form id="logoutform" action="{{ route('logout') }}" method="POST" style="display: none;">
        {{ csrf_field() }}
    </form>

    <style>
        /* Dashboard Layout */
        .dashboard-wrapper {
            display: flex;
            min-height: 100vh;
            background-color: var(--bg-secondary);
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: var(--bg-primary);
            border-right: 1px solid var(--color-gray-200);
            overflow-y: auto;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 999;
            transition: transform var(--transition-base);
        }

        .sidebar.collapsed {
            transform: translateX(-100%);
        }

        .sidebar-header {
            padding: var(--spacing-lg);
            border-bottom: 1px solid var(--color-gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .sidebar-header h2 {
            margin: 0;
            color: var(--color-primary);
            font-size: var(--font-size-2xl);
        }

        .sidebar-toggle {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            font-size: var(--font-size-lg);
        }

        .sidebar-nav {
            padding: var(--spacing-md) 0;
        }

        .nav-section {
            margin-bottom: var(--spacing-lg);
        }

        .nav-section-title {
            font-size: var(--font-size-xs);
            font-weight: var(--font-weight-semibold);
            color: var(--text-light);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: var(--spacing-sm) var(--spacing-lg);
            margin: 0;
        }

        .nav-section ul {
            list-style: none;
            padding: 0;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: var(--spacing-md);
            padding: var(--spacing-sm) var(--spacing-lg);
            color: var(--text-secondary);
            text-decoration: none;
            transition: all var(--transition-fast);
        }

        .nav-link:hover {
            background-color: var(--bg-secondary);
            color: var(--color-primary);
        }

        .nav-link.active {
            background-color: var(--color-primary-50);
            color: var(--color-primary);
            font-weight: var(--font-weight-semibold);
            border-left: 3px solid var(--color-primary);
            padding-left: calc(var(--spacing-lg) - 3px);
        }

        .nav-icon {
            font-size: var(--font-size-lg);
        }

        .sidebar-footer {
            padding: var(--spacing-md) var(--spacing-lg);
            border-top: 1px solid var(--color-gray-200);
            text-align: center;
            font-size: var(--font-size-sm);
            color: var(--text-light);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            display: flex;
            flex-direction: column;
        }

        /* Navbar */
        .navbar {
            background: var(--bg-primary);
            border-bottom: 1px solid var(--color-gray-200);
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar-container {
            padding: var(--spacing-md) var(--spacing-lg);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: var(--spacing-lg);
        }

        .navbar-hamburger {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            font-size: var(--font-size-lg);
        }

        .navbar-hamburger span {
            display: block;
            width: 24px;
            height: 2px;
            background: var(--text-primary);
            margin: 4px 0;
            transition: var(--transition-fast);
        }

        .navbar-brand h1 {
            margin: 0;
            font-size: var(--font-size-2xl);
            color: var(--text-primary);
        }

        .navbar-right {
            display: flex;
            align-items: center;
            gap: var(--spacing-lg);
        }

        .navbar-item.dropdown {
            position: relative;
        }

        .navbar-user {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            background: none;
            border: none;
            cursor: pointer;
            padding: var(--spacing-sm) var(--spacing-md);
            border-radius: var(--radius-lg);
            transition: var(--transition-fast);
        }

        .navbar-user:hover {
            background-color: var(--bg-secondary);
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            background: var(--color-primary);
            color: white;
            border-radius: var(--radius-full);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: var(--font-weight-bold);
            font-size: var(--font-size-lg);
        }

        .dropdown-menu {
            position: absolute;
            right: 0;
            top: 100%;
            background: var(--bg-primary);
            border: 1px solid var(--color-gray-200);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            min-width: 200px;
            margin-top: var(--spacing-sm);
            display: none;
            z-index: 1000;
        }

        .dropdown-menu.show {
            display: block;
        }

        .dropdown-item {
            display: block;
            width: 100%;
            padding: var(--spacing-sm) var(--spacing-lg);
            text-align: left;
            background: none;
            border: none;
            color: var(--text-secondary);
            text-decoration: none;
            cursor: pointer;
            transition: var(--transition-fast);
        }

        .dropdown-item:hover {
            background-color: var(--bg-secondary);
            color: var(--color-primary);
        }

        .dropdown-divider {
            margin: var(--spacing-xs) 0;
            border: none;
            border-top: 1px solid var(--color-gray-200);
        }

        /* Content */
        .content-alerts {
            padding: var(--spacing-lg);
        }

        .page-content {
            flex: 1;
            padding: var(--spacing-lg);
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: var(--spacing-lg);
            border-top: 1px solid var(--color-gray-200);
            background: var(--bg-primary);
            color: var(--text-light);
            font-size: var(--font-size-sm);
        }

        .footer p {
            margin: 0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .sidebar-toggle {
                display: block;
            }

            .main-content {
                margin-left: 0;
            }

            .navbar-hamburger {
                display: block;
            }

            .navbar-container {
                padding: var(--spacing-md);
            }

            .navbar-brand h1 {
                font-size: var(--font-size-xl);
            }

            .page-content {
                padding: var(--spacing-md);
            }
        }

        /* Alert Styles */
        .content-alerts .alert {
            margin-bottom: var(--spacing-md);
        }

        .alert-close {
            float: right;
            background: none;
            border: none;
            font-size: var(--font-size-lg);
            cursor: pointer;
            color: inherit;
            padding: 0;
        }

        .alert-title {
            margin-bottom: var(--spacing-sm);
            font-weight: var(--font-weight-semibold);
        }

        .alert ul {
            padding-left: var(--spacing-xl);
        }

        .alert li {
            margin-bottom: var(--spacing-xs);
        }

        /* DataTables polish */
        .dataTables_wrapper {
            font-size: var(--font-size-sm);
        }

        .dataTables_wrapper .dt-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: var(--spacing-md);
            flex-wrap: wrap;
            margin-bottom: var(--spacing-md);
        }

        .dataTables_wrapper .dt-left,
        .dataTables_wrapper .dt-right {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            flex-wrap: wrap;
        }

        .dataTables_wrapper .dataTables_length label,
        .dataTables_wrapper .dataTables_filter label {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            color: var(--text-secondary);
            font-weight: var(--font-weight-medium);
        }

        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            padding: 8px 12px;
            border: 1px solid var(--color-gray-200);
            border-radius: var(--radius-md);
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 38px;
        }

        .dataTables_wrapper .dataTables_filter input::placeholder {
            color: var(--text-light);
        }

        .dataTables_wrapper .dt-buttons {
            display: flex;
            gap: var(--spacing-sm);
            flex-wrap: wrap;
        }

        .dataTables_wrapper .dt-buttons .dt-button {
            border: 1px solid transparent;
            background: var(--color-primary);
            color: #fff;
            border-radius: var(--radius-md);
            padding: 8px 12px;
            font-weight: var(--font-weight-semibold);
            box-shadow: var(--shadow-sm);
            transition: var(--transition-fast);
            cursor: pointer;
        }

        .dataTables_wrapper .dt-buttons .dt-button:hover {
            background: var(--color-primary-600);
        }

        .dataTables_wrapper .dt-buttons .dt-button:active,
        .dataTables_wrapper .dt-buttons .dt-button:focus {
            outline: none;
            box-shadow: 0 0 0 3px var(--color-primary-100);
        }

        table.dataTable thead th,
        table.dataTable thead td {
            border-bottom: none;
        }

        table.dataTable tbody tr {
            border-bottom: none;
        }

        table.dataTable tbody td {
            border: none;
        }

        table.dataTable tbody tr:nth-child(even) {
            background: var(--bg-primary);
        }

        table.dataTable tbody tr:hover {
            background: var(--color-primary-50);
        }

        table.dataTable tbody td.select-checkbox::before {
            margin-top: -8px;
            border-radius: 4px;
            border: 1px solid var(--color-gray-300);
        }

        table.dataTable tr.selected td.select-checkbox::before {
            background-color: var(--color-primary);
            border-color: var(--color-primary);
            box-shadow: none;
        }

        table.dataTable.dtr-inline.collapsed > tbody > tr > td.dtr-control:before,
        table.dataTable.dtr-inline.collapsed > tbody > tr > th.dtr-control:before {
            background-color: var(--color-primary);
            box-shadow: none;
        }

        .dataTables_wrapper .dataTables_info {
            color: var(--text-secondary);
            padding-top: var(--spacing-sm);
        }

        .dataTables_wrapper .dt-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: var(--spacing-md);
            flex-wrap: wrap;
        }

        .dataTables_wrapper .dataTables_paginate {
            padding-top: var(--spacing-sm);
            display: flex;
            gap: var(--spacing-xs);
            align-items: center;
            justify-content: flex-end;
            flex-wrap: wrap;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border: 1px solid var(--color-gray-200);
            border-radius: var(--radius-md);
            padding: 6px 10px;
            background: var(--bg-primary);
            color: var(--text-secondary) !important;
            transition: var(--transition-fast);
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: var(--color-primary);
            color: #fff !important;
            border-color: var(--color-primary);
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
            background: var(--bg-secondary);
            color: var(--text-light) !important;
            border-color: var(--color-gray-200);
            cursor: not-allowed;
        }
    </style>

    <script>
        // Sidebar and Navbar Toggle
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('collapsed');
        });

        document.getElementById('navbarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });

        // Dropdown Menu Toggle
        document.getElementById('userDropdown')?.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('userMenu').classList.toggle('show');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('userDropdown');
            const menu = document.getElementById('userMenu');
            if (!dropdown?.contains(e.target) && !menu?.contains(e.target)) {
                menu?.classList.remove('show');
            }
        });

        // Close alerts
        document.querySelectorAll('.alert-close').forEach(btn => {
            btn.addEventListener('click', function() {
                this.closest('.alert').style.display = 'none';
            });
        });
    </script>

    <!-- Vendor scripts for legacy admin pages (DataTables, Select2, etc.) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script>

        <script>
            // Initialize Select2 and wire Select all / Deselect all for multi-selects
            if (window.jQuery) {
                jQuery(function($){
                    $('.select2').each(function(){
                        const opts = { width: '100%' };
                        $(this).select2(opts);
                    });

                    $('.select-all').on('click', function() {
                        const $select = $(this).closest('.form-group').find('select[multiple]');
                        $select.find('option').prop('selected', true);
                        $select.trigger('change');
                    });

                    $('.deselect-all').on('click', function() {
                        const $select = $(this).closest('.form-group').find('select[multiple]');
                        $select.find('option').prop('selected', false);
                        $select.trigger('change');
                    });
                });
            }
        </script>

        <script>
            // Global DataTables defaults for admin pages
            if (window.jQuery) {
                jQuery(function($){
                    $.extend(true, $.fn.dataTable.defaults, {
                        responsive: { details: { type: 'inline', target: 'tr' } },
                        autoWidth: false,
                        pagingType: 'full_numbers',
                        pageLength: 10,
                        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                        dom: "<'dt-toolbar'<'dt-left'lB><'dt-right'f>>t<'dt-footer'ip>",
                        buttons: [
                            { extend: 'copy', text: 'Copy' },
                            { extend: 'csv', text: 'CSV' },
                            { extend: 'print', text: 'Print' }
                        ],
                        select: {
                            style: 'multi+shift',
                            selector: 'td:first-child'
                        },
                        columnDefs: [
                            { targets: 0, orderable: false, searchable: false, className: 'select-checkbox', width: '32px' },
                            { targets: -1, orderable: false, searchable: false, responsivePriority: 1 }
                        ],
                        language: {
                            lengthMenu: 'Show _MENU_ entries',
                            search: 'Search',
                            info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                            paginate: { first: 'First', last: 'Last', next: 'Next', previous: 'Previous' }
                        }
                    });

                    // Add placeholder to search input
                    $(document).on('draw.dt', function(){
                        $('div.dataTables_filter input[type=search]').attr('placeholder','Search records');
                    });
                });
            }
        </script>

        <!-- Custom Admin Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/axios@1.6.0/dist/axios.min.js"></script>
        <script src="{{ asset('js/app.js') }}"></script>
        @yield('scripts')
</body>
</html>
