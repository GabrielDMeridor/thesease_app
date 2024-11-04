@php
    $current_route = request()->route()->getName();
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

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ $current_route == 'OVPRIdashboard' ? 'active' : '' }}">
        <a href="{{ route('OVPRIdashboard') }}" class="nav-link">
            <i class="fas fa-fw fa-home"></i>
            <span>Home</span>
        </a>
    </li>

    <!-- Nav Item - Account -->
    <li class="nav-item {{ $current_route == 'library.account' ? 'active' : '' }}">
        <a href="{{ route('ovpri.account') }}" class="nav-link">
            <i class="fas fa-fw fa-user"></i>
            <span>Account</span>
        </a>
    </li>


    <!-- Nav Item - Thesis Checking -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseThesisCheck"
            aria-expanded="true" aria-controls="collapseThesisCheck">
            <i class="fas fa-fw fa-file"></i>
            <span>Thesis Checking</span>
        </a>
        <div id="collapseThesisCheck" class="collapse" aria-labelledby="headingThesisCheck" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item" href="{{ route('ovpri.route1') }}">Routing Form 1</a>
                <a class="collapse-item" href="#">Routing Form 2</a>
            </div>
        </div>
    </li>
</ul>

