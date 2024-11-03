@php
    $current_route = request()->route()->getName();
    $user = Auth::user();
@endphp

<!-- Main Sidebar Container -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion sasidebar" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
        <div class="sidebar-brand-text mx-3" style="font-size: 24px; font-weight: bold;">
            Thes<span style="color: yellow;">Ease</span>
        </div>
    </a>
    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    @if ($user->verification_status === 'unverified' || $user->verification_status === 'disapproved')

        <!-- Nav Item - Home for unverified or disapproved users -->
        <li class="nav-item {{ $current_route == 'gssstudent.partialdashboard' ? 'active' : '' }}">
            <a href="{{ route('gssstudent.partialdashboard') }}" class="nav-link">
                <i class="fas fa-fw fa-home"></i>
                <span>Home</span>
            </a>
        </li>

        <!-- Nav Item - Account (Always visible) -->
        <li class="nav-item {{ $current_route == 'gsstudent.account' ? 'active' : '' }}">
            <a href="{{ route('gsstudent.account') }}" class="nav-link">
                <i class="fas fa-fw fa-user"></i>
                <span>Account</span>
            </a>
        </li>

    @elseif ($user->verification_status === 'verified')

        <!-- Nav Item - Home for verified users -->
        <li class="nav-item {{ $current_route == 'GSSdashboard' ? 'active' : '' }}">
            <a href="{{ route('GSSdashboard') }}" class="nav-link">
                <i class="fas fa-fw fa-home"></i>
                <span>Home</span>
            </a>
        </li>

        <!-- Nav Item - Account (Always visible) -->
        <li class="nav-item {{ $current_route == 'gsstudent.account' ? 'active' : '' }}">
            <a href="{{ route('gsstudent.account') }}" class="nav-link">
                <i class="fas fa-fw fa-user"></i>
                <span>Account</span>
            </a>
        </li>

        <!-- Additional Nav Items for verified users -->
        <li class="nav-item {{ $current_route == 'gsstudent.calendar' ? 'active' : '' }}">
        <a href="{{ route('gsstudent.calendar') }}" class="nav-link">
            <i class="fas fa-fw fa-calendar"></i>
            <span>Calendar</span>
        </a>
        </li>
        <!-- Nav Item - Thesis Checking -->
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseThesisCheck"
                aria-expanded="true" aria-controls="collapseThesisCheck">
                <i class="fas fa-fw fa-file"></i>
                <span>Thesis Requirements</span>
            </a>
            <div id="collapseThesisCheck" class="collapse" aria-labelledby="headingThesisCheck" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('gsstudent.route1') }}">Routing Form 1</a>
                <a class="collapse-item" href="#">Routing Form 2</a>
                </div>
            </div>
        </li>

        <!-- Nav Item - Thesis Repository -->
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-fw fa-book"></i>
                <span>Thesis Repository</span>
            </a>
        </li>
    @endif
</ul>
