<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Catalogue | GIHQS</title>

    <!-- Meta Description (SEO) -->
    <meta name="description" content="A central catalogue of GIHQS certifications, courses, learning modules, toolkits, and professional development offerings.">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,500;1,600&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" />

    <!-- Tailwind CSS (compiled via app.css, or loaded fallback) -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Inter', 'sans-serif'],
                            serif: ['Playfair Display', 'serif'],
                        },
                        colors: {
                            gihqs: {
                                dark: '#0f2c25',
                                forest: '#1a3c34',
                                lightGreen: '#235247',
                                gold: '#d09b3c',
                                beige: '#fbf9f4',
                            }
                        }
                    }
                }
            }
        </script>
    @endif

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #fcfbfa;
        }
        .serif-title {
            font-family: 'Playfair Display', serif;
        }
        /* Custom pastel colors based on service type */
        .card-certification { background-color: #faf6eb; border-color: #f3e9cf; }
        .card-course { background-color: #f0f7f4; border-color: #deece6; }
        .card-webinar { background-color: #f9f2ef; border-color: #f1e2da; }
        .card-module { background-color: #f1f5f8; border-color: #e0e9f0; }
        .card-toolkit { background-color: #f8f1f7; border-color: #ebdceb; }
        .card-workshop { background-color: #f3f1fa; border-color: #e5e0f5; }

        /* Custom color badges */
        .badge-text-certification { color: #a46d16; background-color: #f3e9cf; }
        .badge-text-course { color: #167a57; background-color: #deece6; }
        .badge-text-webinar { color: #a24b23; background-color: #f1e2da; }
        .badge-text-module { color: #23658f; background-color: #e0e9f0; }
        .badge-text-toolkit { color: #7a2b70; background-color: #ebdceb; }
        .badge-text-workshop { color: #5832a8; background-color: #e5e0f5; }

        /* Primary button colors */
        .btn-certification { background-color: #b07c1b; }
        .btn-certification:hover { background-color: #936512; }
        .btn-course { background-color: #147250; }
        .btn-course:hover { background-color: #0d553a; }
        .btn-webinar { background-color: #99441d; }
        .btn-webinar:hover { background-color: #7b3313; }
        .btn-module { background-color: #205e85; }
        .btn-module:hover { background-color: #164766; }
        .btn-toolkit { background-color: #702666; }
        .btn-toolkit:hover { background-color: #55194d; }
        .btn-workshop { background-color: #5832a8; }
        .btn-workshop:hover { background-color: #432187; }

        /* Dot colors */
        .dot-certification { background-color: #b07c1b; }
        .dot-course { background-color: #147250; }
        .dot-webinar { background-color: #99441d; }
        .dot-module { background-color: #205e85; }
        .dot-toolkit { background-color: #702666; }
        .dot-workshop { background-color: #5832a8; }
    </style>
</head>
<body class="text-slate-800 antialiased">

    <!-- Top Navigation Header -->
    <header class="sticky top-0 z-50 bg-white border-b border-slate-100 shadow-sm transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-11 h-11 rounded-full bg-gihqs-forest text-white font-serif text-xl font-bold shadow-md shadow-emerald-950/20">
                        G
                    </div>
                    <div>
                        <div class="serif-title text-xl font-bold text-gihqs-forest tracking-tight leading-none">GIHQS</div>
                        <div class="text-[9px] font-semibold text-slate-500 uppercase tracking-widest leading-none mt-1">Global Institute for Healthcare Quality & Safety</div>
                    </div>
                </div>

                <!-- Nav links (Desktop) -->
                <nav class="hidden lg:flex items-center gap-8">
                    <a href="#" class="text-xs font-semibold uppercase tracking-wider text-slate-600 hover:text-gihqs-forest transition-colors">Certifications <i class="fa-solid fa-angle-down text-[10px] ml-0.5"></i></a>
                    <a href="#" class="text-xs font-semibold uppercase tracking-wider text-slate-600 hover:text-gihqs-forest transition-colors">Learning <i class="fa-solid fa-angle-down text-[10px] ml-0.5"></i></a>
                    <a href="#" class="text-xs font-semibold uppercase tracking-wider text-slate-600 hover:text-gihqs-forest transition-colors">Accreditation <i class="fa-solid fa-angle-down text-[10px] ml-0.5"></i></a>
                    <a href="#" class="text-xs font-semibold uppercase tracking-wider text-slate-600 hover:text-gihqs-forest transition-colors">Advisory <i class="fa-solid fa-angle-down text-[10px] ml-0.5"></i></a>
                    <a href="#" class="text-xs font-semibold uppercase tracking-wider text-slate-600 hover:text-gihqs-forest transition-colors">Membership <i class="fa-solid fa-angle-down text-[10px] ml-0.5"></i></a>
                    <a href="#" class="text-xs font-semibold uppercase tracking-wider text-slate-600 hover:text-gihqs-forest transition-colors">About <i class="fa-solid fa-angle-down text-[10px] ml-0.5"></i></a>
                </nav>

                <!-- Login Button -->
                <div class="flex items-center gap-4">
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-full bg-gihqs-forest hover:bg-gihqs-lightGreen text-white text-xs font-semibold uppercase tracking-wider shadow-md hover:shadow-lg transition-all duration-200">
                        <i class="fa-regular fa-user text-xs"></i> Login
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Wrapper -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <!-- Hero Section -->
        <section class="mb-14">
            <div class="relative bg-gihqs-forest rounded-2xl overflow-hidden shadow-xl p-8 sm:p-12 lg:p-16 flex flex-col justify-center min-h-[280px]">
                <!-- Subtle decorative background pattern -->
                <div class="absolute inset-0 opacity-[0.04] bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px]"></div>
                <div class="relative z-10 max-w-3xl">
                    <span class="inline-block px-3 py-1 rounded-full bg-white/10 text-white text-[10px] font-bold uppercase tracking-widest mb-4">
                        Global Institute for Healthcare Quality & Safety
                    </span>
                    <h1 class="serif-title text-3xl sm:text-4xl lg:text-5xl font-bold text-white leading-tight mb-4">
                        GIHQS Professional <span class="italic text-[#dfbc7b]">Catalogue</span>
                    </h1>
                    <p class="text-emerald-100/90 text-sm sm:text-base leading-relaxed font-light">
                        A central catalogue of GIHQS certifications, courses, learning modules, toolkits, and future professional development offerings designed to support healthcare quality, patient safety, and high-reliability healthcare systems.
                    </p>
                </div>
            </div>
        </section>

        <!-- Search & Filter Layout -->
        <section class="mb-10">
            <div class="flex flex-col gap-6">
                <!-- Section Header -->
                <div>
                    <h2 class="serif-title text-2xl font-bold text-slate-900 mb-1">Browse All Offerings</h2>
                    <p class="text-xs text-slate-500">Featured items should be selected dynamically from admin using a featured toggle.</p>
                </div>

                <!-- Search Bar & Dropdown -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <!-- Search Input -->
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-magnifying-glass text-sm"></i>
                        </div>
                        <input type="text" id="searchInput" placeholder="Search certifications, courses, learning modules, or toolkits..." 
                            class="block w-full pl-11 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-gihqs-forest focus:border-transparent shadow-sm transition-all text-sm">
                    </div>

                    <!-- Filter Dropdown -->
                    <div class="relative w-full sm:w-60">
                        <select id="typeSelector" class="block w-full px-4 py-3 pr-10 rounded-xl border border-slate-200 bg-white text-slate-700 appearance-none focus:outline-none focus:ring-2 focus:ring-gihqs-forest focus:border-transparent shadow-sm transition-all text-sm font-medium">
                            <option value="All">All Types</option>
                            <option value="Certification">Certifications</option>
                            <option value="Course">Courses</option>
                            <option value="Webinar">Webinars</option>
                            <option value="Module">Modules</option>
                            <option value="Toolkit">Toolkits</option>
                            <option value="Workshop">Workshops</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                            <i class="fa-solid fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>

                <!-- Tabs (Featured, Trending, Popular) -->
                <div class="flex items-center gap-3 border-b border-slate-100 pb-4 overflow-x-auto">
                    <button onclick="toggleTab('all')" id="tab-all" class="tab-btn px-5 py-2 rounded-full text-xs font-semibold transition-all duration-200 bg-gihqs-forest text-white shadow-sm">
                        All Offerings
                    </button>
                    <button onclick="toggleTab('featured')" id="tab-featured" class="tab-btn px-5 py-2 rounded-full text-xs font-semibold text-slate-600 hover:text-slate-900 border border-slate-200 bg-white hover:bg-slate-50 transition-all duration-200">
                        Featured
                    </button>
                    <button onclick="toggleTab('trending')" id="tab-trending" class="tab-btn px-5 py-2 rounded-full text-xs font-semibold text-slate-600 hover:text-slate-900 border border-slate-200 bg-white hover:bg-slate-50 transition-all duration-200">
                        Trending
                    </button>
                    <button onclick="toggleTab('popular')" id="tab-popular" class="tab-btn px-5 py-2 rounded-full text-xs font-semibold text-slate-600 hover:text-slate-900 border border-slate-200 bg-white hover:bg-slate-50 transition-all duration-200">
                        Popular
                    </button>
                </div>
            </div>
        </section>

        <!-- Catalog Grid -->
        <section>
            <!-- Items Container -->
            <div id="catalogGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($items as $item)
                    <!-- Card Item -->
                    <div class="catalog-card rounded-2xl border p-6 flex flex-col justify-between transition-all duration-300 hover:scale-[1.02] hover:shadow-lg card-{{ strtolower($item->service_type) }}"
                         data-title="{{ strtolower($item->title) }}"
                         data-short-title="{{ strtolower($item->short_title) }}"
                         data-description="{{ strtolower($item->short_description) }}"
                         data-features="{{ strtolower($item->features->pluck('description')->join(' ')) }}"
                         data-type="{{ $item->service_type }}"
                         data-is-featured="{{ $item->is_feature ? 'true' : 'false' }}"
                         data-is-trending="{{ $item->is_trending ? 'true' : 'false' }}"
                         data-is-popular="{{ $item->is_popular ? 'true' : 'false' }}">
                        
                        <!-- Top Badges Section -->
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <span class="px-3 py-1 rounded text-[9px] font-bold uppercase tracking-wider badge-text-{{ strtolower($item->service_type) }}">
                                    {{ $item->service_type }}
                                </span>
                                
                                @if ($item->is_feature)
                                    <span class="px-3 py-1 rounded text-[9px] font-bold uppercase tracking-wider text-amber-700 bg-amber-100/80">
                                        Featured
                                    </span>
                                @elseif ($item->is_trending)
                                    <span class="px-3 py-1 rounded text-[9px] font-bold uppercase tracking-wider text-orange-700 bg-orange-100/80">
                                        Trending
                                    </span>
                                @elseif ($item->is_popular)
                                    <span class="px-3 py-1 rounded text-[9px] font-bold uppercase tracking-wider text-blue-700 bg-blue-100/80">
                                        Popular
                                    </span>
                                @endif
                            </div>

                            <!-- Title -->
                            <h3 class="serif-title text-lg font-bold text-slate-900 mb-2 leading-snug">
                                {{ $item->title }}
                            </h3>

                            <!-- Short Description -->
                            <p class="text-xs text-slate-600 mb-4 leading-relaxed line-clamp-3">
                                {{ $item->short_description }}
                            </p>

                            <!-- Features List -->
                            @if ($item->features->count() > 0)
                                <ul class="space-y-2 mb-6">
                                    @foreach ($item->features as $feature)
                                        <li class="flex items-start gap-2 text-xs text-slate-700">
                                            <span class="w-1.5 h-1.5 rounded-full mt-1.5 shrink-0 dot-{{ strtolower($item->service_type) }}"></span>
                                            <span>{{ $feature->description }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                        <!-- Price and Buttons Footer -->
                        <div class="border-t border-slate-100/50 pt-4">
                            <!-- Price display -->
                            <div class="flex items-baseline justify-between mb-4">
                                <span class="text-xs text-slate-500 font-medium">Regular: ${{ number_format($item->price_regular, 0) }}</span>
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-slate-900">${{ number_format($item->price_final, 0) }}</div>
                                    <div class="text-[10px] text-slate-500">For Premium Members: ${{ number_format($item->price_final * 0.85, 0) }}</div>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="flex gap-2">
                                @php
                                    $btnText = 'Apply';
                                    if (in_array($item->service_type, ['Course', 'Webinar', 'Module', 'Workshop'])) {
                                        $btnText = 'Enroll';
                                    } elseif ($item->service_type === 'Toolkit') {
                                        $btnText = 'Access';
                                    }
                                @endphp
                                <a href="#" class="flex-1 inline-flex items-center justify-center px-4 py-2.5 rounded-full text-white text-xs font-semibold tracking-wider transition-colors duration-200 btn-{{ strtolower($item->service_type) }}">
                                    {{ $btnText }}
                                </a>
                                <a href="#" class="px-4 py-2.5 rounded-full border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 text-xs font-semibold tracking-wider transition-colors duration-200">
                                    View Details
                                </a>
                            </div>
                        </div>

                    </div>
                @empty
                    <!-- No items indicator -->
                    <div class="col-span-full py-12 text-center text-slate-400">
                        <i class="fa-regular fa-folder-open text-3xl mb-3 block"></i>
                        No offerings available at the moment.
                    </div>
                @endforelse
            </div>

            <!-- Empty Search Results container -->
            <div id="noResults" class="hidden col-span-full py-16 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 text-slate-400 mb-4">
                    <i class="fa-solid fa-magnifying-glass text-2xl"></i>
                </div>
                <h4 class="serif-title text-xl font-bold text-slate-900 mb-1">No matches found</h4>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">Try adjusting your search keywords, service type filter, or status toggles to find what you are looking for.</p>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="bg-gihqs-dark text-slate-300 mt-20 pt-16 pb-8 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
                <!-- Branding column -->
                <div class="lg:col-span-2 space-y-4">
                    <div class="flex items-center gap-3 text-white">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-white text-gihqs-dark font-serif text-lg font-bold">
                            G
                        </div>
                        <div>
                            <div class="serif-title text-lg font-bold tracking-tight">GIHQS</div>
                            <div class="text-[8px] font-semibold uppercase tracking-widest text-slate-400 leading-none">Global Institute for Healthcare Quality & Safety</div>
                        </div>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed max-w-sm">
                        Advancing Healthcare Professionals for High-Reliability Healthcare Systems. Committed to raising standards in clinical quality and security.
                    </p>
                    <div class="inline-block px-4 py-1.5 rounded-full border border-emerald-500/20 text-emerald-400/90 text-[10px] font-semibold tracking-wider uppercase bg-emerald-500/5">
                        Towards Zero Preventable Harm
                    </div>
                </div>

                <!-- GIHQS links column -->
                <div>
                    <h4 class="serif-title text-white text-sm font-semibold uppercase tracking-wider mb-4">GIHQS</h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="#" class="hover:text-white transition-colors text-slate-400">Our Story</a></li>
                        <li><a href="#" class="hover:text-white transition-colors text-slate-400">Vision, Mission & Values</a></li>
                        <li><a href="#" class="hover:text-white transition-colors text-slate-400">Learning</a></li>
                        <li><a href="#" class="hover:text-white transition-colors text-slate-400">Accreditation</a></li>
                        <li><a href="#" class="hover:text-white transition-colors text-slate-400">Membership</a></li>
                        <li><a href="#" class="hover:text-white transition-colors text-slate-400">Contact Us</a></li>
                    </ul>
                </div>

                <!-- Contact details column -->
                <div class="space-y-3 text-xs">
                    <h4 class="serif-title text-white text-sm font-semibold uppercase tracking-wider mb-4">Contact</h4>
                    <p class="flex items-start gap-2 text-slate-400">
                        <i class="fa-solid fa-location-dot mt-0.5 text-gihqs-gold"></i>
                        <span>1200 Mountain Road PL NE<br>STE R Albuquerque, NM 87110</span>
                    </p>
                    <p class="flex items-center gap-2 text-slate-400">
                        <i class="fa-solid fa-phone text-gihqs-gold"></i>
                        <span>+1 347 763 9554</span>
                    </p>
                    <p class="flex items-center gap-2 text-slate-400">
                        <i class="fa-solid fa-envelope text-gihqs-gold"></i>
                        <span>info@gihqs.com</span>
                    </p>
                    <p class="text-[10px] text-slate-500 pt-1 leading-snug">
                        Refunds & purchase support:<br>
                        <a href="mailto:support@gihqs.com" class="text-gihqs-gold hover:underline">support@gihqs.com</a>
                    </p>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="border-t border-slate-800 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-[10px] text-slate-500">
                    &copy; 2026 Global Institute for Healthcare Quality & Safety (GIHQS). All rights reserved.
                </p>
                <div class="flex items-center gap-4 text-[10px] text-slate-500">
                    <a href="#" class="hover:text-slate-300 transition-colors">Privacy Policy</a>
                    <span>&middot;</span>
                    <a href="#" class="hover:text-slate-300 transition-colors">Terms of Use</a>
                    <span>&middot;</span>
                    <a href="#" class="hover:text-slate-300 transition-colors">Terms & Conditions of Purchase</a>
                    <span>&middot;</span>
                    <a href="#" class="hover:text-slate-300 transition-colors">Refund Policy</a>
                    <span>&middot;</span>
                    <a href="#" class="hover:text-slate-300 transition-colors">Disclaimer</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Interactive Client-side Search and Filter Script -->
    <script>
        let currentTab = 'all';

        function toggleTab(tabName) {
            currentTab = tabName;
            
            // Update active/inactive tab buttons classes
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.className = "tab-btn px-5 py-2 rounded-full text-xs font-semibold border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 hover:text-slate-900 transition-all duration-200";
            });

            const activeBtn = document.getElementById('tab-' + tabName);
            activeBtn.className = "tab-btn px-5 py-2 rounded-full text-xs font-semibold bg-gihqs-forest text-white shadow-sm transition-all duration-200";

            // Run filter
            filterCatalog();
        }

        function filterCatalog() {
            const searchVal = document.getElementById('searchInput').value.trim().toLowerCase();
            const typeVal = document.getElementById('typeSelector').value;
            const cards = document.querySelectorAll('.catalog-card');
            let visibleCount = 0;

            cards.forEach(card => {
                const title = card.getAttribute('data-title');
                const shortTitle = card.getAttribute('data-short-title') || '';
                const desc = card.getAttribute('data-description');
                const features = card.getAttribute('data-features');
                const type = card.getAttribute('data-type');
                const isFeatured = card.getAttribute('data-is-featured') === 'true';
                const isTrending = card.getAttribute('data-is-trending') === 'true';
                const isPopular = card.getAttribute('data-is-popular') === 'true';

                // Check search match
                const matchesSearch = !searchVal || 
                                     title.includes(searchVal) || 
                                     shortTitle.includes(searchVal) || 
                                     desc.includes(searchVal) || 
                                     features.includes(searchVal);

                // Check type filter match
                const matchesType = (typeVal === 'All') || (type === typeVal);

                // Check tab filter match
                let matchesTab = true;
                if (currentTab === 'featured') {
                    matchesTab = isFeatured;
                } else if (currentTab === 'trending') {
                    matchesTab = isTrending;
                } else if (currentTab === 'popular') {
                    matchesTab = isPopular;
                }

                // Overall check
                if (matchesSearch && matchesType && matchesTab) {
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Handle no results display
            const noResults = document.getElementById('noResults');
            const grid = document.getElementById('catalogGrid');
            if (visibleCount === 0) {
                noResults.classList.remove('hidden');
            } else {
                noResults.classList.add('hidden');
            }
        }

        // Bind events
        document.getElementById('searchInput').addEventListener('input', filterCatalog);
        document.getElementById('typeSelector').addEventListener('change', filterCatalog);
    </script>
</body>
</html>
