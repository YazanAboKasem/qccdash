<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MY SEPHORA Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .login-bg {
            background: linear-gradient(135deg, #171717 0%, #3a1116 100%);
        }
    </style>
</head>
<body class="login-bg min-h-screen flex items-center justify-center p-6 font-sans">

    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex w-16 h-16 rounded-2xl bg-[#E2001A] items-center justify-center font-black text-white text-3xl shadow-lg mb-4">S</div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">MY SEPHORA</h1>
            <p class="text-slate-300 mt-2 text-sm">Loyalty Quiz Dashboard</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 p-8">
            <h2 class="text-xl font-bold text-slate-800 mb-6 text-center">Administrator Sign In</h2>

            @if ($errors->any())
                <div class="mb-4 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-lg text-sm">
                    <ul class="list-disc pl-4 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="/login" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required 
                           class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition duration-150"
                           placeholder="admin@sephora.local">
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">Password</label>
                    <input type="password" name="password" id="password" required 
                           class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition duration-150"
                           placeholder="••••••••">
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded text-blue-600 focus:ring-blue-500 border-slate-300">
                        <span class="text-sm text-slate-600">Remember device</span>
                    </label>
                </div>

                <button type="submit" 
                        class="w-full bg-slate-900 hover:bg-blue-800 text-white font-bold py-3.5 px-4 rounded-xl shadow-lg hover:shadow-blue-500/20 transition duration-150 cursor-pointer">
                    Sign In
                </button>
            </form>
        </div>

        <div class="text-center mt-8">
            <span class="text-slate-400 text-xs">&copy; 2026 MY SEPHORA. All Rights Reserved.</span>
        </div>
    </div>

</body>
</html>
