<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ADQCC Dashboard') - Kiosk Survey Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .glass-sidebar {
            background: linear-gradient(185deg, #0f172a 0%, #1e293b 100%);
            border-right: 1px solid rgba(255, 255, 255, 0.05);
        }
        .nav-item-active {
            background: linear-gradient(90deg, rgba(212, 175, 55, 0.15) 0%, rgba(212, 175, 55, 0.02) 100%);
            border-left: 4px solid #D4AF37;
            color: #f8fafc;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen flex">

    <!-- Sidebar -->
    <aside class="w-64 glass-sidebar flex flex-col min-h-screen text-slate-300 shrink-0">
        <!-- Sidebar Brand -->
        <div class="h-16 flex items-center px-6 border-b border-slate-800">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-lg bg-yellow-500 flex items-center justify-center font-bold text-slate-900 text-lg shadow-md shadow-yellow-500/25">Q</div>
                <span class="font-bold text-white text-lg tracking-wide uppercase">ADQCC Kiosk</span>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="/dashboard" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-slate-800 hover:text-white transition duration-200 {{ request()->is('dashboard') || request()->is('/') ? 'nav-item-active text-white' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span>Dashboard</span>
            </a>
            
            <div class="pt-4 pb-2 px-4 uppercase text-xs font-semibold text-slate-500 tracking-wider">Campaigns & Surveys</div>
            
            @php 
                $surveys = \App\Models\Survey::whereNull('deleted_at')->get();
            @endphp
            @foreach($surveys as $s)
                <a href="{{ route('surveys.show', $s) }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-slate-800 hover:text-white transition duration-200 {{ request()->is('surveys/' . $s->id) || request()->is('surveys/' . $s->id . '/*') ? 'nav-item-active text-white' : '' }}">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span class="truncate">{{ $s->title['en'] ?? 'Survey Details' }}</span>
                </a>
            @endforeach
        </nav>

        <!-- Sidebar User Footer -->
        <div class="p-4 border-t border-slate-800 flex items-center justify-between">
            <div class="flex items-center space-x-3 truncate">
                <div class="w-10 h-10 rounded-full bg-slate-700 flex items-center justify-center font-semibold text-white">
                    {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                </div>
                <div class="truncate">
                    <div class="text-sm font-semibold text-white truncate">{{ Auth::user()->name ?? 'Admin User' }}</div>
                    <div class="text-xs text-slate-400 truncate">{{ Auth::user()->email ?? '' }}</div>
                </div>
            </div>
            
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="p-2 text-slate-400 hover:text-red-400 transition cursor-pointer" title="Logout">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content wrapper -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Top Navigation Header -->
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-8 shadow-sm shrink-0">
            <div class="text-xl font-bold text-slate-800">
                @yield('header_title', 'Dashboard')
            </div>
            
            <div class="flex items-center space-x-4">
                <span class="text-sm text-slate-500 font-semibold">Abu Dhabi Quality and Conformity Council</span>
            </div>
        </header>

        <!-- Main Body -->
        <main class="flex-1 overflow-y-auto p-8">
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-800 flex items-center space-x-2 shadow-sm">
                    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-lg text-rose-800 flex items-center space-x-2 shadow-sm">
                    <svg class="w-5 h-5 text-rose-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @yield('scripts')
</body>
</html>
