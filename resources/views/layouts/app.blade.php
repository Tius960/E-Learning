<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Ruang Kelas') · Ruang Kelas</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v=20">
</head>
<body>
    @if (auth()->check() && auth()->user()->role === 'admin')
    <div class="admin-shell">
        <aside class="admin-sidebar" id="admin-sidebar">
            <a class="brand admin-brand" href="{{ route('admin.courses.index') }}">
                <span class="brand-mark" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M4.5 5.5h5.3c1.2 0 2.2 1 2.2 2.2v11c0-1.2-1-2.2-2.2-2.2H4.5v-11ZM19.5 5.5h-5.3c-1.2 0-2.2 1-2.2 2.2v11c0-1.2 1-2.2 2.2-2.2h5.3v-11Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                <span>ruang kelas</span>
            </a>
            <span class="sidebar-label">Menu admin</span>
            <nav class="sidebar-nav">
                <a class="{{ request()->routeIs('admin.courses.*', 'admin.lessons.*') ? 'active' : '' }}" href="{{ route('admin.courses.index') }}"><span aria-hidden="true">▦</span> Kelola course</a>
            </nav>
            <div class="sidebar-user">
                <div><strong>{{ auth()->user()->name }}</strong><span>Administrator</span></div>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="sidebar-logout" type="submit" aria-label="Keluar" title="Keluar">⇥</button></form>
            </div>
        </aside>
        <div class="admin-main">
            <header class="admin-topbar">
                <button class="menu-toggle" type="button" aria-controls="admin-sidebar" aria-expanded="false" aria-label="Buka menu">☰</button>
                <span class="admin-breadcrumb">Area admin <span>/</span> {{ request()->routeIs('admin.courses.*') ? 'Kelola course' : 'Materi course' }}</span>
                <span class="role-pill">Admin</span>
            </header>
            <main class="page admin-page">
    @else
    <header class="topbar">
        <div class="topbar-inner">
            <a class="brand" href="{{ auth()->check() ? (auth()->user()->role === 'admin' ? route('admin.courses.index') : route('student.courses.index')) : route('login') }}">
                <span class="brand-mark" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M4.5 5.5h5.3c1.2 0 2.2 1 2.2 2.2v11c0-1.2-1-2.2-2.2-2.2H4.5v-11ZM19.5 5.5h-5.3c-1.2 0-2.2 1-2.2 2.2v11c0-1.2 1-2.2 2.2-2.2h5.3v-11Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span><span>ruang kelas</span>
            </a>
            @auth
                <nav class="nav-links"><a href="{{ route('student.courses.index') }}">Katalog belajar</a></nav>
                <div class="account">
                    <span class="account-name">{{ auth()->user()->name }}</span>
                    <span class="role-pill">Siswa</span>
                    <form class="account-logout" method="POST" action="{{ route('logout') }}">@csrf<button class="button button-quiet button-small" type="submit">Keluar</button></form>
                </div>
                <details class="mobile-menu">
                    <summary class="menu-toggle" aria-label="Buka menu">☰</summary>
                    <div class="mobile-menu-panel"><a href="{{ route('student.courses.index') }}">Katalog belajar</a><form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Keluar</button></form></div>
                </details>
            @endauth
        </div>
    </header>

    <main class="page student-page">
    @endif
        @if (session('status'))<div class="notice notice-success" role="status">{{ session('status') }}</div>@endif
        @if (session('success'))<div class="notice notice-success" role="status">{{ session('success') }}</div>@endif
        @if ($errors->any())
            <div class="notice notice-error" role="alert">
                <strong>Periksa kembali data berikut:</strong>
                <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        @yield('content')
    </main>
    @if (auth()->check() && auth()->user()->role === 'admin')
            <footer class="footer admin-footer">© {{ date('Y') }} Ruang Kelas</footer>
        </div>
    </div>
    @else
        <footer class="footer">© {{ date('Y') }} Ruang Kelas</footer>
    @endif
    @if (auth()->check() && auth()->user()->role === 'admin')
    <dialog class="confirm-dialog" id="confirm-dialog">
        <div class="confirm-symbol" aria-hidden="true">⌫</div>
        <h2 id="confirm-title">Hapus course ini?</h2>
        <p id="confirm-message"></p>
        <span class="confirm-warning">Tindakan ini tidak dapat dibatalkan.</span>
        <div class="confirm-actions">
            <button class="button button-secondary" type="button" data-confirm-cancel>Batal</button>
            <form id="confirm-action-form"><button class="button button-delete" type="submit">Hapus</button></form>
        </div>
    </dialog>
    <script>
        document.querySelector('.menu-toggle[aria-controls]')?.addEventListener('click', (event) => {
            const button = event.currentTarget;
            const open = document.body.classList.toggle('sidebar-open');
            button.setAttribute('aria-expanded', open ? 'true' : 'false');
            button.setAttribute('aria-label', open ? 'Tutup menu' : 'Buka menu');
        });
        document.addEventListener('click', (event) => {
            if (!document.body.classList.contains('sidebar-open') || event.target.closest('.admin-sidebar, .menu-toggle')) return;
            document.body.classList.remove('sidebar-open');
            const button = document.querySelector('.menu-toggle[aria-controls]');
            button?.setAttribute('aria-expanded', 'false');
            button?.setAttribute('aria-label', 'Buka menu');
        });
        document.addEventListener('keydown', (event) => {
            if (event.key !== 'Escape') return;
            document.body.classList.remove('sidebar-open');
            document.querySelector('.menu-toggle[aria-controls]')?.setAttribute('aria-expanded', 'false');
        });
        const confirmDialog = document.getElementById('confirm-dialog');
        const confirmMessage = document.getElementById('confirm-message');
        let pendingForm;
        document.addEventListener('submit', (event) => {
            const form = event.target;
            if (!(form instanceof HTMLFormElement) || !form.dataset.confirm || form === document.getElementById('confirm-action-form')) return;
            event.preventDefault();
            pendingForm = form;
            confirmMessage.textContent = form.dataset.confirm;
            confirmDialog.showModal();
        });
        document.querySelector('[data-confirm-cancel]').addEventListener('click', () => confirmDialog.close());
        document.getElementById('confirm-action-form').addEventListener('submit', (event) => {
            event.preventDefault();
            confirmDialog.close();
            if (pendingForm) HTMLFormElement.prototype.submit.call(pendingForm);
        });
    </script>
    @endif
</body>
</html>
