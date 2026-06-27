<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SmartSIM') }}</title>

        <!-- Favicon -->
        <link rel="shortcut icon" href="{{ asset('assets/images/logo/favicon.png') }}" type="image/png">

        <!-- Bootstrap 5 GRID ONLY for premium responsiveness without Reboot/Reset overrides -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap-grid.min.css" rel="stylesheet" />
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Lucide Icons CDN -->
        <script src="https://unpkg.com/lucide@latest"></script>
        
        <!-- SweetAlert2 CDN -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        @stack('styles')
    </head>
    <body class="font-sans antialiased text-slate-800 h-full">
        <!-- Main Layout Container (Alpine.js State) -->
        <div x-data="{ sidebarOpen: false }" class="h-full flex overflow-hidden">
            
            <!-- Sidebar Navigation -->
            @include('layouts.partials.sidebar')

            <!-- Content Area Wrapper -->
            <div class="flex-1 flex flex-col min-w-0 overflow-y-auto lg:h-screen lg:overflow-y-auto">
                <!-- Sticky Header -->
                @include('layouts.partials.header')

                <!-- Main Content Slot -->
                <main class="flex-grow p-4 sm:p-6 lg:p-8 bg-slate-50/50">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <script>
            // Initialize Lucide Icons
            lucide.createIcons();

            // Form submission processing state visual handler
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function (e) {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        // Prevent double submission
                        if (submitBtn.classList.contains('is-processing')) {
                            e.preventDefault();
                            return;
                        }
                        
                        submitBtn.classList.add('is-processing');
                        submitBtn.style.opacity = '0.8';
                        
                        // Look for span and icon to replace
                        const textSpan = submitBtn.querySelector('span');
                        const icon = submitBtn.querySelector('[data-lucide]');
                        
                        if (textSpan) {
                            textSpan.textContent = 'Processing...';
                        } else {
                            submitBtn.textContent = 'Processing...';
                        }
                        
                        if (icon) {
                            icon.setAttribute('data-lucide', 'loader-2');
                            icon.classList.add('animate-spin');
                        } else {
                            // Create icon if not present
                            const newIcon = document.createElement('i');
                            newIcon.setAttribute('data-lucide', 'loader-2');
                            newIcon.className = 'w-4 h-4 animate-spin ml-2 inline-block';
                            submitBtn.appendChild(newIcon);
                        }
                        
                        lucide.createIcons();
                    }
                });
            });
        </script>

        @if(Auth::check() && Auth::user()->hasVerifiedEmail() && (empty(Auth::user()->first_name) || empty(Auth::user()->last_name) || empty(Auth::user()->phone) || empty(Auth::user()->state) || empty(Auth::user()->lga) || empty(Auth::user()->address)))
            @include('pages.dashboard.kyc')
        @endif

        @if (session('welcome'))
            <!-- Welcome Celebratory Modal Overlay -->
            <div x-data="{ welcomeOpen: true }" x-show="welcomeOpen" 
                 class="fixed inset-0 z-[99999] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
                <div @click.away="welcomeOpen = false" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     class="bg-white rounded-3xl border border-slate-100 shadow-2xl max-w-lg w-full p-8 text-center relative overflow-hidden animate-in fade-in zoom-in-95 duration-300">
                    
                    <!-- Decorative Green/Blue Gradient Background Elements -->
                    <div class="absolute -top-24 -left-24 w-48 h-48 bg-emerald-500/10 rounded-full blur-3xl"></div>
                    <div class="absolute -top-24 -right-24 w-48 h-48 bg-[#42517c]/10 rounded-full blur-3xl"></div>

                    <!-- Icon -->
                    <div class="mx-auto w-16 h-16 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-500 mb-6 border border-emerald-100/50">
                        <i data-lucide="party-popper" class="w-8 h-8"></i>
                    </div>

                    <!-- Header -->
                    <h2 class="text-2xl font-extrabold text-slate-900 font-display mb-3">
                        Profile Setup Complete!
                    </h2>
                    
                    <p class="text-sm text-slate-600 leading-relaxed mb-6">
                        {{ session('welcome') }}
                    </p>

                    <!-- Core Value Selling Points Grid -->
                    <div class="grid grid-cols-3 gap-3 mb-8">
                        <div class="p-3 bg-slate-50 border border-slate-100 rounded-2xl">
                            <div class="text-emerald-500 mb-1 flex justify-center">
                                <i data-lucide="zap" class="w-5 h-5"></i>
                            </div>
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Speed</span>
                            <span class="block text-xs font-semibold text-slate-700">Instant Delivery</span>
                        </div>
                        <div class="p-3 bg-slate-50 border border-slate-100 rounded-2xl">
                            <div class="text-[#42517c] mb-1 flex justify-center">
                                <i data-lucide="tag" class="w-5 h-5"></i>
                            </div>
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Rates</span>
                            <span class="block text-xs font-semibold text-slate-700">Cheapest Plans</span>
                        </div>
                        <div class="p-3 bg-slate-50 border border-slate-100 rounded-2xl">
                            <div class="text-blue-500 mb-1 flex justify-center">
                                <i data-lucide="shield-check" class="w-5 h-5"></i>
                            </div>
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Security</span>
                            <span class="block text-xs font-semibold text-slate-700">Fully Protected</span>
                        </div>
                    </div>

                    <!-- Button Actions -->
                    <button type="button" @click="welcomeOpen = false" 
                            class="w-full py-3.5 px-6 bg-gradient-to-r from-[#42517c] to-[#55699e] hover:from-[#354268] hover:to-[#42517c] text-white font-semibold text-sm rounded-xl shadow-lg shadow-[#42517c]/10 hover:shadow-[#42517c]/20 active:scale-[0.98] transition-all duration-200 font-display">
                        Start Exploring Services
                    </button>
                </div>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                });
                // Immediate fallback to render icons if page is already loaded
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            </script>
        @endif

        @stack('scripts')
    </body>
</html>
