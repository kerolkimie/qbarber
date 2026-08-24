<nav class="navbar navbar-expand-md navbar-brand-bar navbar-dark py-3">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('admin.dashboard') }}">
            <span class="pole-dot"></span> Blade &amp; Fade — Admin
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavMenu"
                aria-controls="adminNavMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="adminNavMenu">
            <ul class="navbar-nav me-auto mt-3 mt-md-0 gap-md-2">
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'text-white fw-semibold' : 'text-white-50' }}">Dashboard</a>
                </li>

                {{-- Perniagaan: Owner, Cawangan, Tiket --}}
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle {{ request()->routeIs(['admin.owners.*','admin.branches.*','admin.tickets.*']) ? 'text-white fw-semibold' : 'text-white-50' }}"
                       role="button" data-bs-toggle="dropdown" aria-expanded="false">Perniagaan</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item {{ request()->routeIs('admin.owners.*') ? 'fw-semibold' : '' }}" href="{{ route('admin.owners.index') }}">Owner</a></li>
                        <li><a class="dropdown-item {{ request()->routeIs('admin.branches.*') ? 'fw-semibold' : '' }}" href="{{ route('admin.branches.index') }}">Cawangan</a></li>
                        <li><a class="dropdown-item {{ request()->routeIs('admin.tickets.*') ? 'fw-semibold' : '' }}" href="{{ route('admin.tickets.index') }}">Tiket</a></li>
                    </ul>
                </li>

                {{-- Langganan: Subscription, Pakej --}}
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle {{ request()->routeIs(['admin.subscriptions.*','admin.plans.*']) ? 'text-white fw-semibold' : 'text-white-50' }}"
                       role="button" data-bs-toggle="dropdown" aria-expanded="false">Langganan</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item {{ request()->routeIs('admin.subscriptions.*') ? 'fw-semibold' : '' }}" href="{{ route('admin.subscriptions.index') }}">Subscription</a></li>
                        <li><a class="dropdown-item {{ request()->routeIs('admin.plans.*') ? 'fw-semibold' : '' }}" href="{{ route('admin.plans.index') }}">Pakej</a></li>
                    </ul>
                </li>

                {{-- Ejen: Senarai Ejen, Komisen --}}
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle {{ request()->routeIs(['admin.agents.*','admin.commissions.*']) ? 'text-white fw-semibold' : 'text-white-50' }}"
                       role="button" data-bs-toggle="dropdown" aria-expanded="false">Ejen</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item {{ request()->routeIs('admin.agents.*') ? 'fw-semibold' : '' }}" href="{{ route('admin.agents.index') }}">Senarai Ejen</a></li>
                        <li><a class="dropdown-item {{ request()->routeIs('admin.commissions.*') ? 'fw-semibold' : '' }}" href="{{ route('admin.commissions.index') }}">Komisen</a></li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.inquiries.index') }}" class="nav-link {{ request()->routeIs('admin.inquiries.*') ? 'text-white fw-semibold' : 'text-white-50' }}">
                        Pertanyaan
                        @if (($newInquiryCount ?? \App\Models\ContactInquiry::where('status', 'new')->count()) > 0)
                            <span class="badge text-bg-danger ms-1">{{ $newInquiryCount ?? \App\Models\ContactInquiry::where('status', 'new')->count() }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.logs.index') }}" class="nav-link {{ request()->routeIs('admin.logs.*') ? 'text-white fw-semibold' : 'text-white-50' }}">Log</a>
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
