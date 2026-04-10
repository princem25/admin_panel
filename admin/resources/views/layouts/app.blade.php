<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Loading Animation 12 by Martin van Driel */
        #loader-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: radial-gradient(circle farthest-corner at center, #3C4B57 0%, #1C262B 100%);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease-in-out;
        }

        body.page-exit #loader-overlay {
            opacity: 1;
            visibility: visible;
        }

        .loader {
            position: relative;
            width: 64px;
            height: 64px;
            border-radius: 50%;
            perspective: 800px;
        }

        .inner {
            position: absolute;
            box-sizing: border-box;
            width: 100%;
            height: 100%;
            border-radius: 50%;  
        }

        .inner.one {
            left: 0%;
            top: 0%;
            animation: rotate-one 1s linear infinite;
            border-bottom: 3px solid #EFEFFA;
        }

        .inner.two {
            right: 0%;
            top: 0%;
            animation: rotate-two 1s linear infinite;
            border-right: 3px solid #EFEFFA;
        }

        .inner.three {
            right: 0%;
            bottom: 0%;
            animation: rotate-three 1s linear infinite;
            border-top: 3px solid #EFEFFA;
        }

        @keyframes rotate-one {
            0% { transform: rotateX(35deg) rotateY(-45deg) rotateZ(0deg); }
            100% { transform: rotateX(35deg) rotateY(-45deg) rotateZ(360deg); }
        }

        @keyframes rotate-two {
            0% { transform: rotateX(50deg) rotateY(10deg) rotateZ(0deg); }
            100% { transform: rotateX(50deg) rotateY(10deg) rotateZ(360deg); }
        }

        @keyframes rotate-three {
            0% { transform: rotateX(35deg) rotateY(55deg) rotateZ(0deg); }
            100% { transform: rotateX(35deg) rotateY(55deg) rotateZ(360deg); }
        }
    </style>
</head>

<body class="font-sans antialiased">
    <!-- Loader Overlay -->
    <div id="loader-overlay">
        <div class="loader">
            <div class="inner one"></div>
            <div class="inner two"></div>
            <div class="inner three"></div>
        </div>
    </div>

    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        @include('layouts.navigation')

        <!-- Toast Notifications Container -->
        <div class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-50" id="toast-container"></div>

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white dark:bg-gray-800 shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>

    @stack('scripts')

    @auth
        <script type="module">
            document.addEventListener('DOMContentLoaded', () => {
                // Page Fade Out on Link Click
                document.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', e => {
                        const href = link.getAttribute('href');
                        
                        // Ignore special links
                        if (!href || 
                            href.startsWith('#') || 
                            href.startsWith('javascript:') || 
                            link.target === '_blank' || 
                            e.ctrlKey || e.shiftKey || e.metaKey || e.altKey ||
                            link.classList.contains('no-transition')) {
                            return;
                        }

                        // Check if internal link
                        const isInternal = href.indexOf(window.location.host) !== -1 || href.startsWith('/') || !href.includes('://');
                        
                        if (isInternal) {
                            e.preventDefault();
                            document.body.classList.add('page-exit');
                            setTimeout(() => {
                                window.location.href = href;
                            }, 300);
                        }
                    });
                });

                // Handle Browsing Presence Channel
                window.Echo.join('store.browsing')
                    .here((users) => {
                        console.log('Active users count:', users.length);
                    })
                    .joining((user) => {
                        console.log('User joined:', user.name);
                    })
                    .leaving((user) => {
                        console.log('User left:', user.name);
                    })
                    .error((error) => {
                        console.error('Presence channel error:', error);
                    });
            });

            // Re-reveal on back button (BFCache)
            window.addEventListener('pageshow', (event) => {
                if (event.persisted) {
                    document.body.classList.remove('page-exit');
                }
            });
        </script>
    @endauth
</body>

</html>
