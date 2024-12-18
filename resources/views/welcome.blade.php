{{-- resources/views/welcome.blade.php --}}
@extends('layouts.landing')

@section('content')
<div class="relative">
    <!-- Hero Section -->
    <div class="relative min-h-screen flex items-center">
        <!-- Background Image -->
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-gradient-to-r from-black to-transparent z-10"></div>
            <img src="https://images.unsplash.com/photo-1553877522-43269d4ea984?q=80&w=2070&auto=format&fit=crop" 
                 alt="Background" 
                 class="w-full h-full object-cover filter brightness-50">
        </div>
        
        <div class="container mx-auto px-4 z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div class="space-y-8">
                    <h1 class="text-5xl lg:text-7xl font-bold leading-tight">
                        <span class="gradient-text">Smart Inventory</span>
                        <br>Management System
                    </h1>
                    <p class="text-xl text-gray-300">
                        Transform your business operations with our cutting-edge inventory management solution. Experience real-time tracking, advanced analytics, and seamless integration.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('register') }}" 
                           class="px-8 py-4 bg-gradient-to-r from-blue-500 to-emerald-500 rounded-full text-white font-semibold hover:scale-105 transition-all">
                            Start Free Trial
                        </a>
                        <a href="#features" 
                           class="px-8 py-4 bg-white/10 backdrop-blur-lg rounded-full text-white font-semibold hover:bg-white/20 transition-all">
                            Learn More
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div id="features" class="py-24 bg-gradient-to-b from-black via-gray-900 to-black">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4">
                    <span class="gradient-text">Powerful Features</span>
                </h2>
                <p class="text-gray-400 text-lg">Everything you need to manage your inventory efficiently</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature Cards -->
                <div class="p-8 rounded-2xl bg-white/5 backdrop-blur-lg hover:bg-white/10 transition-all border border-gray-800">
                    <div class="w-14 h-14 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-4">Real-time Tracking</h3>
                    <p class="text-gray-400">Monitor your inventory levels in real-time with automatic updates and notifications.</p>
                </div>

                <div class="p-8 rounded-2xl bg-white/5 backdrop-blur-lg hover:bg-white/10 transition-all border border-gray-800">
                    <div class="w-14 h-14 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-4">Advanced Analytics</h3>
                    <p class="text-gray-400">Gain insights with detailed reports and analytics to make data-driven decisions.</p>
                </div>

                <div class="p-8 rounded-2xl bg-white/5 backdrop-blur-lg hover:bg-white/10 transition-all border border-gray-800">
                    <div class="w-14 h-14 bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-4">Secure Access</h3>
                    <p class="text-gray-400">Role-based access control ensuring your data is always protected.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="py-24 bg-black">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="text-4xl font-bold gradient-text mb-2">99.9%</div>
                    <div class="text-gray-400">Uptime</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold gradient-text mb-2">50K+</div>
                    <div class="text-gray-400">Users</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold gradient-text mb-2">24/7</div>
                    <div class="text-gray-400">Support</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold gradient-text mb-2">1M+</div>
                    <div class="text-gray-400">Transactions</div>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="py-24 bg-gradient-to-b from-black to-gray-900">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                <div class="p-8 rounded-3xl bg-white/5 backdrop-blur-lg border border-gray-800">
                    <h2 class="text-4xl font-bold mb-8">
                        <span class="gradient-text">Ready to Get Started?</span>
                    </h2>
                    <p class="text-gray-400 text-lg mb-8">Join thousands of businesses that trust our system for their inventory management needs.</p>
                    <a href="{{ route('register') }}" 
                       class="px-12 py-4 bg-gradient-to-r from-blue-500 to-emerald-500 rounded-full text-white font-semibold hover:scale-105 transition-all inline-block">
                        Start Your Free Trial
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection