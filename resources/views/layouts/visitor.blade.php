<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'veenix - Nonton Film dan Drama Korea Terbaru')</title>

    <!-- SEO Meta Tags -->
    <meta name="description"
        content="@yield('description', 'Nonton film dan drama Korea terbaru subtitle Indonesia. Streaming movie & TV series dengan kualitas HD gratis di veenix.')">
    <meta name="keywords"
        content="@yield('keywords', 'nonton film, streaming movie, drama Korea, film Indonesia, TV series, subtitle Indonesia, gratis, HD')">
    <meta name="author" content="veenix">
    <meta name="robots" content="index, follow">
    <meta name="language" content="id">
    <meta name="revisit-after" content="1 days">

    <!-- Canonical URL -->
    <link rel="canonical" href="@yield('canonical', url()->current())">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="@yield('og:title', 'veenix - Nonton Film dan Drama Korea Terbaru')">
    <meta property="og:description"
        content="@yield('og:description', 'Nonton film dan drama Korea terbaru subtitle Indonesia. Streaming movie & TV series dengan kualitas HD gratis di veenix.')">
    <meta property="og:url" content="@yield('og:url', url()->current())">
    <meta property="og:type" content="@yield('og:type', 'website')">
    <meta property="og:site_name" content="veenix">
    <meta property="og:image" content="@yield('og:image', asset('images/og-image.jpg'))">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="id_ID">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('twitter:title', 'veenix - Nonton Film dan Drama Korea Terbaru')">
    <meta name="twitter:description"
        content="@yield('twitter:description', 'Nonton film dan drama Korea terbaru subtitle Indonesia. Streaming movie & TV series dengan kualitas HD gratis di veenix.')">
    <meta name="twitter:image" content="@yield('twitter:image', asset('images/og-image.jpg'))">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Additional Meta Tags -->
    <meta name="theme-color" content="#8b5cf6">
    <meta name="msapplication-TileColor" content="#8b5cf6">
    <meta name="application-name" content="veenix">
    <meta name="apple-mobile-web-app-title" content="veenix">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    <!-- Preconnect to external domains -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://cdn.tailwindcss.com">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#8b5cf6',
                        secondary: '#6d28d9',
                    }
                }
            }
        }
    </script>
    @stack('styles')
    <style>
        .iframe-container {
            position: relative;
            width: 100%;
            height: 0;
            padding-top: 0;
            padding-bottom: 56.25%;
            /* 16:9 aspect ratio */
        }

        .iframe-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }

        .movie-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .movie-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(139, 92, 246, 0.3);
        }

        .slider-grab {
            cursor: grab;
        }

        .slider-grab:active {
            cursor: grabbing;
        }

        .slider-grab * {
            pointer-events: none;
        }

        /* Hide scrollbar for mobile horizontal scrolling */
        .scrollbar-hide {
            -ms-overflow-style: none;
            /* Internet Explorer 10+ */
            scrollbar-width: none;
            /* Firefox */
        }

        .scrollbar-hide::-webkit-scrollbar {
            display: none;
            /* Safari and Chrome */
        }
    </style>
</head>

<body class="bg-gray-900 text-white">
    <!-- Navbar -->
    <nav class="fixed top-0 w-full z-50 bg-black/90 backdrop-blur-md border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Left side - Logo and Navigation -->
                <div class="flex items-center space-x-8">
                    <!-- Logo -->
                    <div class="flex-shrink-0">
                        <a href="/" class="text-2xl font-bold text-violet-400">veenix</a>
                    </div>

                    <!-- Navigation Menu (Desktop) -->
                    <div class="hidden md:block">
                        <div class="ml-10 flex items-baseline space-x-4">
                            <a href="/"
                                class="text-white hover:text-violet-400 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->is('/') ? 'text-violet-400' : '' }}">Home</a>

                            <!-- Genre Dropdown -->
                            <div class="relative group">
                                <button
                                    class="text-gray-300 hover:text-violet-400 px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center">
                                    Genre
                                    <i class="fas fa-chevron-down ml-1 text-xs"></i>
                                </button>
                                <div
                                    class="absolute left-0 mt-2 w-56 bg-gray-800 rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                                    <div class="py-1 max-h-96 overflow-y-auto">
                                        @php
                                        $genres = \App\Models\Genre::orderBy('name')->get();
                                        @endphp
                                        @foreach($genres as $genre)
                                        <a href="/genre/{{ $genre->slug }}"
                                            class="block px-4 py-2 text-sm text-gray-300 hover:bg-violet-600 hover:text-white">
                                            {{ $genre->name }}
                                        </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>


                            <!-- Year Dropdown -->
                            <div class="relative group">
                                <button
                                    class="text-gray-300 hover:text-violet-400 px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center">
                                    Year
                                    <i class="fas fa-chevron-down ml-1 text-xs"></i>
                                </button>
                                <div
                                    class="absolute left-0 mt-2 w-48 bg-gray-800 rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                                    <div class="py-1 max-h-60 overflow-y-auto">
                                        <a href="/year/2025"
                                            class="block px-4 py-2 text-sm text-gray-300 hover:bg-violet-600 hover:text-white">2025</a>
                                        <a href="/year/2024"
                                            class="block px-4 py-2 text-sm text-gray-300 hover:bg-violet-600 hover:text-white">2024</a>
                                        <a href="/year/2023"
                                            class="block px-4 py-2 text-sm text-gray-300 hover:bg-violet-600 hover:text-white">2023</a>
                                        <a href="/year/2022"
                                            class="block px-4 py-2 text-sm text-gray-300 hover:bg-violet-600 hover:text-white">2022</a>
                                        <a href="/year/2021"
                                            class="block px-4 py-2 text-sm text-gray-300 hover:bg-violet-600 hover:text-white">2021</a>
                                        <a href="/year/2020"
                                            class="block px-4 py-2 text-sm text-gray-300 hover:bg-violet-600 hover:text-white">2020</a>
                                        <a href="/year/2019"
                                            class="block px-4 py-2 text-sm text-gray-300 hover:bg-violet-600 hover:text-white">2019</a>
                                        <a href="/year/2018"
                                            class="block px-4 py-2 text-sm text-gray-300 hover:bg-violet-600 hover:text-white">2018</a>
                                        <a href="/year/2017"
                                            class="block px-4 py-2 text-sm text-gray-300 hover:bg-violet-600 hover:text-white">2017</a>
                                        <a href="/year/2016"
                                            class="block px-4 py-2 text-sm text-gray-300 hover:bg-violet-600 hover:text-white">2016</a>
                                        <a href="/year/2015"
                                            class="block px-4 py-2 text-sm text-gray-300 hover:bg-violet-600 hover:text-white">2015</a>
                                        <a href="/year/2014"
                                            class="block px-4 py-2 text-sm text-gray-300 hover:bg-violet-600 hover:text-white">2014</a>
                                        <a href="/year/2013"
                                            class="block px-4 py-2 text-sm text-gray-300 hover:bg-violet-600 hover:text-white">2013</a>
                                        <a href="/year/2012"
                                            class="block px-4 py-2 text-sm text-gray-300 hover:bg-violet-600 hover:text-white">2012</a>
                                        <a href="/year/2011"
                                            class="block px-4 py-2 text-sm text-gray-300 hover:bg-violet-600 hover:text-white">2011</a>
                                        <a href="/year/2010"
                                            class="block px-4 py-2 text-sm text-gray-300 hover:bg-violet-600 hover:text-white">2010</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right side - Search, Login, and Mobile menu -->
                <div class="flex items-center space-x-4">
                    <!-- Search Bar (Desktop) -->
                    <div class="hidden sm:block relative">
                        <input type="text" placeholder="Search movies..."
                            class="bg-gray-800 text-white placeholder-gray-400 rounded-lg py-2 pl-10 pr-4 w-64 focus:outline-none focus:ring-2 focus:ring-violet-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>

                    <!-- Login Button (Desktop) -->
                    <div class="hidden sm:block">
                        <a href="/login"
                            class="bg-violet-600 hover:bg-violet-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Login
                        </a>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="md:hidden">
                        <button type="button" id="mobile-menu-button"
                            class="text-gray-300 hover:text-white focus:outline-none">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div id="mobile-menu" class="md:hidden hidden bg-black border-t border-gray-800">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="/"
                    class="text-white hover:text-violet-400 block px-3 py-2 rounded-md text-base font-medium">Home</a>

                <!-- Mobile Genre Dropdown -->
                <div class="mobile-dropdown">
                    <button
                        class="mobile-dropdown-button text-gray-300 hover:text-violet-400 w-full text-left px-3 py-2 rounded-md text-base font-medium flex items-center justify-between">
                        Genre
                        <i class="fas fa-chevron-down text-xs transition-transform"></i>
                    </button>
                    <div class="mobile-dropdown-content hidden pl-4 pb-2 max-h-64 overflow-y-auto">
                        @php
                        $mobileGenres = \App\Models\Genre::orderBy('name')->get();
                        @endphp
                        @foreach($mobileGenres as $genre)
                        <a href="/genre/{{ $genre->slug }}"
                            class="block px-3 py-2 text-sm text-gray-300 hover:text-violet-400">
                            {{ $genre->name }}
                        </a>
                        @endforeach
                    </div>
                </div>

                <!-- Mobile Country Dropdown -->
                <div class="mobile-dropdown">
                    <button
                        class="mobile-dropdown-button text-gray-300 hover:text-violet-400 w-full text-left px-3 py-2 rounded-md text-base font-medium flex items-center justify-between">
                        Country
                        <i class="fas fa-chevron-down text-xs transition-transform"></i>
                    </button>
                    <div class="mobile-dropdown-content hidden pl-4 pb-2 max-h-64 overflow-y-auto">
                        @php
                        $mobileCountries = \App\Models\Country::orderBy('english_name')->get();
                        @endphp
                        @foreach($mobileCountries as $country)
                        <a href="/country/{{ $country->slug }}"
                            class="block px-3 py-2 text-sm text-gray-300 hover:text-violet-400">
                            {{ $country->english_name }}
                        </a>
                        @endforeach
                    </div>
                </div>

                <!-- Mobile Year Dropdown -->
                <div class="mobile-dropdown">
                    <button
                        class="mobile-dropdown-button text-gray-300 hover:text-violet-400 w-full text-left px-3 py-2 rounded-md text-base font-medium flex items-center justify-between">
                        Year
                        <i class="fas fa-chevron-down text-xs transition-transform"></i>
                    </button>
                    <div class="mobile-dropdown-content hidden pl-4 pb-2 max-h-48 overflow-y-auto">
                        <a href="/year/2025" class="block px-3 py-2 text-sm text-gray-300 hover:text-violet-400">2025</a>
                        <a href="/year/2024" class="block px-3 py-2 text-sm text-gray-300 hover:text-violet-400">2024</a>
                        <a href="/year/2023" class="block px-3 py-2 text-sm text-gray-300 hover:text-violet-400">2023</a>
                        <a href="/year/2022" class="block px-3 py-2 text-sm text-gray-300 hover:text-violet-400">2022</a>
                        <a href="/year/2021" class="block px-3 py-2 text-sm text-gray-300 hover:text-violet-400">2021</a>
                        <a href="/year/2020" class="block px-3 py-2 text-sm text-gray-300 hover:text-violet-400">2020</a>
                        <a href="/year/2019" class="block px-3 py-2 text-sm text-gray-300 hover:text-violet-400">2019</a>
                        <a href="/year/2018" class="block px-3 py-2 text-sm text-gray-300 hover:text-violet-400">2018</a>
                        <a href="/year/2017" class="block px-3 py-2 text-sm text-gray-300 hover:text-violet-400">2017</a>
                        <a href="/year/2016" class="block px-3 py-2 text-sm text-gray-300 hover:text-violet-400">2016</a>
                        <a href="/year/2015" class="block px-3 py-2 text-sm text-gray-300 hover:text-violet-400">2015</a>
                        <a href="/year/2014" class="block px-3 py-2 text-sm text-gray-300 hover:text-violet-400">2014</a>
                        <a href="/year/2013" class="block px-3 py-2 text-sm text-gray-300 hover:text-violet-400">2013</a>
                        <a href="/year/2012" class="block px-3 py-2 text-sm text-gray-300 hover:text-violet-400">2012</a>
                        <a href="/year/2011" class="block px-3 py-2 text-sm text-gray-300 hover:text-violet-400">2011</a>
                        <a href="/year/2010" class="block px-3 py-2 text-sm text-gray-300 hover:text-violet-400">2010</a>
                    </div>
                </div>

                <!-- Mobile Search -->
                <div class="px-3 py-2">
                    <div class="relative">
                        <input type="text" placeholder="Search movies..."
                            class="bg-gray-800 text-white placeholder-gray-400 rounded-lg py-2 pl-10 pr-4 w-full focus:outline-none focus:ring-2 focus:ring-violet-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Mobile Login Button -->
                <div class="px-3 py-2">
                    <a href="/login"
                        class="bg-violet-600 hover:bg-violet-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center justify-center w-full">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Login
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="pt-16">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-black py-8 px-4 sm:px-6 lg:px-8 border-t border-gray-800">
        <div class="max-w-7xl mx-auto">
            <div class="text-center">
                <h3 class="text-2xl font-bold text-violet-400 mb-4">veenix</h3>
                <p class="text-gray-400 mb-4">Your ultimate destination for streaming movies and TV shows</p>
                <div class="flex justify-center space-x-6">
                    <a href="#" class="text-gray-400 hover:text-violet-400 transition-colors">
                        <i class="fab fa-facebook-f text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-violet-400 transition-colors">
                        <i class="fab fa-twitter text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-violet-400 transition-colors">
                        <i class="fab fa-instagram text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-violet-400 transition-colors">
                        <i class="fab fa-youtube text-xl"></i>
                    </a>
                </div>
                <p class="text-gray-500 text-sm mt-6">&copy; 2024 veenix. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });

        // Mobile dropdown toggle
        document.querySelectorAll('.mobile-dropdown-button').forEach(button => {
            button.addEventListener('click', function() {
                const content = this.nextElementSibling;
                const icon = this.querySelector('i');
                
                content.classList.toggle('hidden');
                icon.classList.toggle('rotate-180');
            });
        });

        // Close mobile menu when clicking on a link
        document.querySelectorAll('#mobile-menu a').forEach(link => {
            link.addEventListener('click', function() {
                const mobileMenu = document.getElementById('mobile-menu');
                mobileMenu.classList.add('hidden');
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
