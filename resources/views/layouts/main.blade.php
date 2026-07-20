@include('partials.header')

<body>
    {{-- Sidebar overlay backdrop for mobile --}}
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    @include('partials.sidemenubar')

    <main class="dashboard-main">
        @include('partials.navbar')

        <div class="dashboard-main-body">
            @yield('content')
        </div>

        @include('partials.footer')
    </main>

    @stack('scripts')

    <script>
    // ── Mobile sidebar toggle ──────────────────────────────────
    (function () {
        const overlay   = document.getElementById('sidebarOverlay');
        const sidebar   = document.querySelector('.sidebar');
        const mobileBtn = document.querySelector('.sidebar-mobile-toggle');
        const closeBtn  = document.querySelector('.sidebar-close-btn');

        function openSidebar() {
            sidebar.classList.add('sidebar-open');
            overlay.classList.add('active');
            document.body.classList.add('sidebar-open-mobile');
        }

        function closeSidebar() {
            sidebar.classList.remove('sidebar-open');
            overlay.classList.remove('active');
            document.body.classList.remove('sidebar-open-mobile');
        }

        if (mobileBtn) mobileBtn.addEventListener('click', openSidebar);
        if (closeBtn)  closeBtn.addEventListener('click', closeSidebar);
        if (overlay)   overlay.addEventListener('click', closeSidebar);

        // Close sidebar on link click (navigate away)
        if (sidebar) {
            sidebar.querySelectorAll('a:not([href="javascript:void(0)"])').forEach(function (a) {
                a.addEventListener('click', function () {
                    if (window.innerWidth < 1200) closeSidebar();
                });
            });
        }
    })();
    </script>

    {{-- Table scroll hint --}}
    <script src="{{ asset('assets/js/table-scroll-hint.js') }}"></script>
</body>
</html>