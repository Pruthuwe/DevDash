@include('partials.header')

<body>
    @include('partials.sidemenubar')

    <main class="dashboard-main">
        @include('partials.navbar')

        <div class="dashboard-main-body">
            @yield('content')
        </div>

        @include('partials.footer')
    </main>

    @stack('scripts')
</body>
</html>
