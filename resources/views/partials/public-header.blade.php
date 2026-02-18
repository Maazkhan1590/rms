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
                @php
                    $user = auth()->user();
                    $isPureFaculty = $user->hasRole('Faculty') && !$user->isAdmin && !$user->isResearchCoordinator() && !$user->isDean();
                @endphp
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" id="submitDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-plus-circle"></i> Submit
                    </a>
                    <div class="dropdown-menu" aria-labelledby="submitDropdown">
                        <a class="dropdown-item" href="{{ route('publications.create') }}"><i class="fas fa-book"></i> Publication</a>
                        <a class="dropdown-item" href="{{ route('grants.create') }}"><i class="fas fa-money-bill-wave"></i> Grant</a>
                        <a class="dropdown-item" href="{{ route('rtn-submissions.create') }}"><i class="fas fa-clipboard-list"></i> RTN Submission</a>
                        <a class="dropdown-item" href="{{ route('bonus-recognitions.create') }}"><i class="fas fa-trophy"></i> Bonus Recognition</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('partnerships.create') }}"><i class="fas fa-handshake"></i> Partnership & MOU</a>
                        <a class="dropdown-item" href="{{ route('commercializations.create') }}"><i class="fas fa-rocket"></i> Commercialization</a>
                        <a class="dropdown-item" href="{{ route('consultancies.create') }}"><i class="fas fa-briefcase"></i> Consultancy & KT</a>
                        <a class="dropdown-item" href="{{ route('awards.create') }}"><i class="fas fa-award"></i> Award</a>
                        <a class="dropdown-item" href="{{ route('research-investments.create') }}"><i class="fas fa-laptop"></i> Research Investment</a>
                        <a class="dropdown-item" href="{{ route('conference-activities.create') }}"><i class="fas fa-microphone"></i> Conference Activity</a>
                        <a class="dropdown-item" href="{{ route('supervision-exams.create') }}"><i class="fas fa-graduation-cap"></i> Supervision & Exam</a>
                        <a class="dropdown-item" href="{{ route('editorial-appointments.create') }}"><i class="fas fa-edit"></i> Editorial Appointment</a>
                        <a class="dropdown-item" href="{{ route('student-involvements.create') }}"><i class="fas fa-user-graduate"></i> Student Involvement</a>
                        <a class="dropdown-item" href="{{ route('research-fellows.create') }}"><i class="fas fa-user-tie"></i> Research Fellow</a>
                        <a class="dropdown-item" href="{{ route('sdg-contributions.create') }}"><i class="fas fa-globe"></i> SDG Contribution</a>
                        <a class="dropdown-item" href="{{ route('internal-fundings.create') }}"><i class="fas fa-dollar-sign"></i> Internal Funding</a>
                        <a class="dropdown-item" href="{{ route('block-fundings.create') }}"><i class="fas fa-cube"></i> Block Funding</a>
                        <a class="dropdown-item" href="{{ route('rtn-course-details.create') }}"><i class="fas fa-book-reader"></i> RTN Course Detail</a>
                        <a class="dropdown-item" href="{{ route('adjunct-professors.propose') }}"><i class="fas fa-chalkboard-teacher"></i> Adjunct Professor</a>
                    </div>
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
                <div class="dropdown">
                    <button class="btn-submit dropdown-toggle" type="button" id="submitBtnDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-paper-plane"></i> Submit
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="submitBtnDropdown">
                        <a class="dropdown-item" href="{{ route('publications.create') }}"><i class="fas fa-book"></i> Publication</a>
                        <a class="dropdown-item" href="{{ route('grants.create') }}"><i class="fas fa-money-bill-wave"></i> Grant</a>
                        <a class="dropdown-item" href="{{ route('rtn-submissions.create') }}"><i class="fas fa-clipboard-list"></i> RTN Submission</a>
                        <a class="dropdown-item" href="{{ route('bonus-recognitions.create') }}"><i class="fas fa-trophy"></i> Bonus Recognition</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('partnerships.create') }}"><i class="fas fa-handshake"></i> Partnership & MOU</a>
                        <a class="dropdown-item" href="{{ route('commercializations.create') }}"><i class="fas fa-rocket"></i> Commercialization</a>
                        <a class="dropdown-item" href="{{ route('consultancies.create') }}"><i class="fas fa-briefcase"></i> Consultancy & KT</a>
                        <a class="dropdown-item" href="{{ route('awards.create') }}"><i class="fas fa-award"></i> Award</a>
                        <a class="dropdown-item" href="{{ route('research-investments.create') }}"><i class="fas fa-laptop"></i> Research Investment</a>
                        <a class="dropdown-item" href="{{ route('conference-activities.create') }}"><i class="fas fa-microphone"></i> Conference Activity</a>
                        <a class="dropdown-item" href="{{ route('supervision-exams.create') }}"><i class="fas fa-graduation-cap"></i> Supervision & Exam</a>
                        <a class="dropdown-item" href="{{ route('editorial-appointments.create') }}"><i class="fas fa-edit"></i> Editorial Appointment</a>
                        <a class="dropdown-item" href="{{ route('student-involvements.create') }}"><i class="fas fa-user-graduate"></i> Student Involvement</a>
                        <a class="dropdown-item" href="{{ route('research-fellows.create') }}"><i class="fas fa-user-tie"></i> Research Fellow</a>
                        <a class="dropdown-item" href="{{ route('sdg-contributions.create') }}"><i class="fas fa-globe"></i> SDG Contribution</a>
                        <a class="dropdown-item" href="{{ route('internal-fundings.create') }}"><i class="fas fa-dollar-sign"></i> Internal Funding</a>
                        <a class="dropdown-item" href="{{ route('block-fundings.create') }}"><i class="fas fa-cube"></i> Block Funding</a>
                        <a class="dropdown-item" href="{{ route('rtn-course-details.create') }}"><i class="fas fa-book-reader"></i> RTN Course Detail</a>
                        <a class="dropdown-item" href="{{ route('adjunct-professors.propose') }}"><i class="fas fa-chalkboard-teacher"></i> Adjunct Professor</a>
                    </div>
                </div>
            @else
                <button class="btn-submit" onclick="window.location.href='{{ route('register') }}'">
                    <i class="fas fa-paper-plane"></i> Submit
                </button>
            @endauth
        </div>
    </div>
</nav>
