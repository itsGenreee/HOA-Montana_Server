<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'HOA Montaña')</title>
     @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        // Light Theme Colors
                        'primary': 'rgb(153, 70, 28)',
                        'on-primary': 'rgb(255, 255, 255)',
                        'primary-container': 'rgb(255, 219, 205)',
                        'on-primary-container': 'rgb(54, 15, 0)',
                        'secondary': 'rgb(121, 89, 0)',
                        'on-secondary': 'rgb(255, 255, 255)',
                        'secondary-container': 'rgb(255, 222, 161)',
                        'on-secondary-container': 'rgb(38, 25, 0)',
                        'tertiary': 'rgb(103, 95, 48)',
                        'on-tertiary': 'rgb(255, 255, 255)',
                        'tertiary-container': 'rgb(239, 227, 169)',
                        'on-tertiary-container': 'rgb(32, 28, 0)',
                        'background': 'rgb(255, 251, 255)',
                        'on-background': 'rgb(32, 26, 24)',
                        'surface': 'rgb(255, 251, 255)',
                        'on-surface': 'rgb(32, 26, 24)',
                        'surface-variant': 'rgb(245, 222, 213)',
                        'on-surface-variant': 'rgb(83, 68, 62)',
                        'outline': 'rgb(133, 115, 108)',
                    }
                }
            }
        }
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.1/src/fill/style.css"
    />
</head>
<body class="min-h-screen bg-background text-on-background">
    <!-- Navigation -->
    <nav class="sticky top-0 z-50 shadow-lg bg-surface">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center flex-shrink-0">
                        <i class="mr-2 text-2xl fas fa-mobile-alt text-primary"></i>
                        <span class="text-2xl font-black text-on-surface" >HOA Montaña</span>
                    </a>
                </div>

                <div class="items-center hidden space-x-8 md:flex">
                    <a href="{{ route('home') }}" class="text-on-surface hover:text-primary transition-colors duration-200 {{ request()->routeIs('home') ? 'text-primary font-medium' : '' }}">
                        Home
                    </a>
                    <a href="{{ route('about') }}" class="text-on-surface hover:text-primary transition-colors duration-200 {{ request()->routeIs('about') ? 'text-primary font-medium' : '' }}">
                        About
                    </a>
                    <a href="{{ route('contact') }}" class="text-on-surface hover:text-primary transition-colors duration-200 {{ request()->routeIs('contact') ? 'text-primary font-medium' : '' }}">
                        Contact
                    </a>
                    <a href="{{ route('download') }}" class="px-4 py-2 font-medium transition-colors duration-200 rounded-lg bg-primary text-on-primary hover:bg-primary/90">
                        Download
                    </a>
                </div>

                <!-- Mobile menu button -->
                <div class="flex items-center md:hidden">
                    <button id="mobile-menu-button" class="text-on-surface hover:text-primary">
                        <i class="text-xl fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div id="mobile-menu" class="hidden border-t md:hidden bg-surface border-surface-variant">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="{{ route('home') }}" class="block px-3 py-2 text-on-surface hover:text-primary {{ request()->routeIs('home') ? 'text-primary font-medium' : '' }}">
                    Home
                </a>
                <a href="{{ route('about') }}" class="block px-3 py-2 text-on-surface hover:text-primary {{ request()->routeIs('about') ? 'text-primary font-medium' : '' }}">
                    About
                </a>
                <a href="{{ route('contact') }}" class="block px-3 py-2 text-on-surface hover:text-primary {{ request()->routeIs('contact') ? 'text-primary font-medium' : '' }}">
                    Contact
                </a>
                <a href="{{ route('download') }}" class="block px-3 py-2 mx-2 font-medium text-center rounded bg-primary text-on-primary">
                    Download
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="mt-16 bg-surface-variant text-on-surface-variant">
        <div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                <div>
                    <h3 class="mb-4 text-lg font-semibold">HOA Montaña</h3>
                    <p class="text-sm">Simplifying community living through modern technology.</p>
                </div>
                <div>
                    <h3 class="mb-4 text-lg font-semibold">Quick Links</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('home') }}" class="transition-colors hover:text-primary">Home</a></li>
                        <li><a href="{{ route('about') }}" class="transition-colors hover:text-primary">About</a></li>
                        <li><a href="{{ route('contact') }}" class="transition-colors hover:text-primary">Contact</a></li>
                        <li><a href="{{ route('download') }}" class="transition-colors hover:text-primary">Download</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="mb-4 text-lg font-semibold">Connect With Us</h3>
                    <div class="flex space-x-4">
                        <a href="#" class="transition-colors text-on-surface-variant hover:text-primary">
                            <i class="text-xl fab fa-facebook"></i>
                        </a>
                        <a href="#" class="transition-colors text-on-surface-variant hover:text-primary">
                            <i class="text-xl fab fa-twitter"></i>
                        </a>
                        <a href="#" class="transition-colors text-on-surface-variant hover:text-primary">
                            <i class="text-xl fab fa-instagram"></i>
                        </a>
                        <a href="#" class="transition-colors text-on-surface-variant hover:text-primary">
                            <i class="text-xl fab fa-linkedin"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="pt-6 mt-8 text-sm text-center border-t border-outline">
                <p>&copy; {{ date('Y') }} HOA Montaña. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });
    </script>
</body>
</html>
