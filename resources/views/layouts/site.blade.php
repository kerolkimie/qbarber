<!doctype html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Barbershop Queue')</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    @stack('styles')
</head>
<body>

    @hasSection('navbar')
        @yield('navbar')
    @else
        <nav class="navbar navbar-brand-bar navbar-dark py-3 mb-4">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="{{ route('landing') }}">
                    <span class="pole-dot"></span> Blade &amp; Fade
                </a>
            </div>
        </nav>
    @endif

    <main class="container pb-5">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle papar/sembunyi untuk SEMUA input password (guna komponen x-password-field)
        document.querySelectorAll('.toggle-password').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var input = document.getElementById(btn.getAttribute('data-target'));
                var icon = document.getElementById('icon-' + btn.getAttribute('data-target'));
                if (!input) return;

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }
            });
        });

        // Auto-gabung prefix +60 dengan nombor yang ditaip (guna komponen x-phone-field)
        document.querySelectorAll('.phone-local-input').forEach(function (input) {
            var hidden = document.getElementById(input.getAttribute('data-hidden-target'));
            if (!hidden) return;

            function sync() {
                var digits = input.value.replace(/[^0-9]/g, '');
                hidden.value = digits ? '+60' + digits.replace(/^0+/, '') : '';
            }

            input.addEventListener('input', sync);
            sync(); // sync sekali bila page load (untuk old() value lepas validation gagal)
        });
    </script>
    @stack('scripts')
</body>
</html>
