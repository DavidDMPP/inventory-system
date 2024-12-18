{{-- resources/views/layouts/landing.blade.php --}}
<!DOCTYPE html>
<html lang="en" class="dark scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Modern Inventory System</title>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }

        .glass-nav {
            background: rgba(17, 25, 40, 0.75);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .gradient-text {
            background: linear-gradient(135deg, #60A5FA, #10B981);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
    </style>
</head>
<body class="bg-black text-white">
    <!-- Navbar -->
    <nav class="glass-nav fixed w-full z-50 transition-all duration-300" x-data="{ atTop: true }" 
         @scroll.window="atTop = window.pageYOffset > 50 ? false : true"
         :class="{ 'py-6': atTop, 'py-4 shadow-2xl': !atTop }">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between">
                <a href="/" class="flex items-center space-x-2">
                    <span class="text-2xl font-bold gradient-text">
                        InventoryPro
                    </span>
                </a>
                <div class="flex items-center space-x-6">
                    <a href="{{ route('login') }}" 
                       class="text-gray-300 hover:text-white transition-colors">
                        Login
                    </a>
                    <a href="{{ route('register') }}" 
                       class="px-6 py-2 bg-gradient-to-r from-blue-500 to-emerald-500 rounded-full text-white font-semibold hover:scale-105 transition-all">
                        Get Started
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Content -->
    @yield('content')

    <!-- Footer -->
    <footer class="bg-black py-12 border-t border-gray-800">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
                <div>
                    <h3 class="text-xl font-bold gradient-text mb-4">
                        InventoryPro
                    </h3>
                    <p class="text-gray-400">Modern inventory management system for your business needs.</p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4 text-white">Product</h4>
                    <ul class="space-y-2">
                        <li><a href="#features" class="text-gray-400 hover:text-white transition-colors">Features</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Pricing</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Documentation</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4 text-white">Company</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">About Us</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Careers</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4 text-white">Legal</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Privacy Policy</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Terms of Service</a></li>
                    </ul>
                </div>
            </div>
            <div class="mt-12 pt-8 border-t border-gray-800 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} InventoryPro. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>