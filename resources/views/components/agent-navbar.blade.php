<nav class="navbar navbar-expand-md navbar-brand-bar navbar-dark py-3">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('agent.dashboard') }}">
            <span class="pole-dot"></span> Blade &amp; Fade — Ejen
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#agentNavMenu"
                aria-controls="agentNavMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="agentNavMenu">
            <ul class="navbar-nav me-auto mt-3 mt-md-0 gap-md-3">
                <li class="nav-item">
                    <a href="{{ route('agent.dashboard') }}" class="nav-link {{ request()->routeIs('agent.dashboard') ? 'text-white fw-semibold' : 'text-white-50' }}">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('agent.owners.index') }}" class="nav-link {{ request()->routeIs('agent.owners.*') ? 'text-white fw-semibold' : 'text-white-50' }}">Cari Owner</a>
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
