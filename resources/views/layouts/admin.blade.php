<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin') | ShangHaejo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Preconnect untuk mempercepat DNS resolve CDN --}}
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>

    {{-- Font Awesome — satu-satunya CSS yang perlu di head --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    {{-- Alpine.js & SweetAlert2 — defer agar tidak blocking render --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Speculation Rules API untuk instant prerender klik menu/sidebar di browser Chromium --}}
    <script type="speculationrules">
    {
        "prerender": [
            {
                "source": "document",
                "where": {
                    "and": [
                        { "href_matches": "/*" },
                        { "not": { "href_matches": "*/logout" } },
                        { "not": { "href_matches": "*destroy*" } },
                        { "not": { "href_matches": "*delete*" } }
                    ]
                },
                "eagerness": "moderate"
            }
        ]
    }
    </script>

    {{-- Loading bar style (cepat & responsif) --}}
    <style>
        #page-loader{position:fixed;top:0;left:0;width:0;height:3px;background:linear-gradient(90deg,#0d9488,#14b8a6,#5eead4);z-index:9999;transition:width .2s ease;pointer-events:none}
        #page-loader.active{width:80%;transition:width .35s cubic-bezier(.1,.05,.0,1)}
        #page-loader.done{width:100%;transition:width .15s ease;opacity:0;transition:width .15s ease, opacity .35s ease .1s}
    </style>
</head>

<body class="bg-gray-50 font-sans" x-data="{ sidebarOpen: false }">
    {{-- Page loading indicator --}}
    <div id="page-loader"></div>

    <div class="flex">

        @include('components.admin.sidebar')

        <main class="flex-1 lg:ml-64 min-h-screen flex flex-col">

            @include('components.admin.navbar')

            <div class="p-8 pt-16 flex-1 overflow-hidden">
                @yield('content')
            </div>

            @include('components.footer')

        </main>
    </div>

    @include('components.admin.toast')

    {{-- WAJIB ADA: agar @push('scripts') dari halaman lain bisa berjalan --}}
    @stack('scripts')

    {{-- Instant Hover Prefetch & Navigation loading script --}}
    <script>
    (function(){
        // 1. Instant Prefetch saat kursor mendekati link sidebar/tombol
        const prefetched = new Set();
        document.addEventListener('pointerenter', function(e) {
            const link = e.target.closest('a[href]');
            if (!link) return;
            const href = link.href;
            if (!href || !href.startsWith(window.location.origin) || prefetched.has(href) || 
                href.includes('/logout') || link.target === '_blank' || link.hasAttribute('download')) return;
            prefetched.add(href);
            const prefetchLink = document.createElement('link');
            prefetchLink.rel = 'prefetch';
            prefetchLink.href = href;
            document.head.appendChild(prefetchLink);
        }, { passive: true, capture: true });

        // 2. Loading bar indicator
        const loader = document.getElementById('page-loader');
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a[href]');
            if (!link) return;
            const href = link.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript:') || 
                link.target === '_blank' || link.hasAttribute('download') ||
                (href.startsWith('http') && !href.startsWith(window.location.origin))) return;
            loader.style.width = '0';
            loader.classList.remove('done');
            void loader.offsetWidth;
            loader.classList.add('active');
        });
        document.addEventListener('submit', function(e) {
            loader.style.width = '0';
            loader.classList.remove('done');
            void loader.offsetWidth;
            loader.classList.add('active');
        });
        window.addEventListener('pageshow', function() {
            loader.classList.remove('active');
            loader.classList.add('done');
        });
    })();
    </script>
</body>
</html>