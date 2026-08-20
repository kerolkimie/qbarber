<nav class="navbar navbar-expand-md navbar-brand-bar navbar-dark py-3">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('owner.dashboard') }}">
            <span class="pole-dot"></span>
            <span class="text-truncate" style="max-width:180px;">{{ auth()->user()->owner->business_name }}</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#ownerNavMenu"
                aria-controls="ownerNavMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="ownerNavMenu">
            <ul class="navbar-nav me-auto mt-3 mt-md-0 gap-md-3">
                <li class="nav-item">
                    <a href="{{ route('owner.dashboard') }}"
                       class="nav-link {{ request()->routeIs('owner.dashboard') ? 'text-white fw-semibold' : 'text-white-50' }}">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('owner.branches.index') }}"
                       class="nav-link {{ request()->routeIs('owner.branches.*') ? 'text-white fw-semibold' : 'text-white-50' }}">Cawangan</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('owner.report') }}"
                       class="nav-link {{ request()->routeIs('owner.report') ? 'text-white fw-semibold' : 'text-white-50' }}">Laporan</a>
                </li>
            </ul>

            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-2 mt-3 mt-md-0">
                <a href="{{ route('profile.edit') }}" class="text-light small opacity-75 text-decoration-none">{{ auth()->user()->name }}</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">Log Keluar</button>
                </form>
            </div>
        </div>
    </div>
</nav>
