<!-- resources/views/auth/login.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen flex items-center justify-center 
bg-gradient-to-br from-[#0f2027] via-[#203a43] to-[#2c5364]">

    <!-- Glow Background -->
    <div class="absolute w-[500px] h-[500px] bg-cyan-400 opacity-20 blur-3xl rounded-full top-10 left-10"></div>
    <div class="absolute w-[500px] h-[500px] bg-blue-500 opacity-20 blur-3xl rounded-full bottom-10 right-10"></div>

    <!-- Glass Card -->
    <div class="relative w-full max-w-sm p-8 rounded-2xl 
    bg-white/10 backdrop-blur-xl border border-white/20 
    shadow-[0_10px_40px_rgba(0,0,0,0.6)] text-white">

        <!-- Icon -->
        <div class="flex justify-center mb-4">
            <div class="w-10 h-10 rounded-full border border-white/30 flex items-center justify-center">
                🔒
            </div>
        </div>

        <!-- Title -->
        <h2 class="text-2xl font-semibold text-center mb-1">
            Login
        </h2>

        <p class="text-center text-sm text-white/70 mb-6">
            Enter your email and password to continue.
        </p>

        <!-- Session Status -->
        @if (session('status'))
            <div class="mb-4 text-green-400 text-sm text-center">
                {{ session('status') }}
            </div>
        @endif

        <!-- Form -->
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Email -->
            <div>
                <input type="email" name="email" value="{{ old('email') }}"
                    placeholder="Email"
                    class="w-full px-4 py-2 rounded-lg 
                    bg-white/10 border border-white/20 
                    placeholder-white/60 text-white 
                    focus:outline-none focus:ring-2 focus:ring-cyan-400">

                @error('email')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <input type="password" name="password"
                    placeholder="Password"
                    class="w-full px-4 py-2 rounded-lg 
                    bg-white/10 border border-white/20 
                    placeholder-white/60 text-white 
                    focus:outline-none focus:ring-2 focus:ring-cyan-400">

                @error('password')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Forgot Password (NEW - same style) -->
            @if (Route::has('password.request'))
                <div class="text-right text-sm">
                    <a href="{{ route('password.request') }}" 
                       class="text-cyan-400 hover:underline">
                        Forgot Password?
                    </a>
                </div>
            @endif

            <!-- Button -->
            <button type="submit"
                class="w-full py-2 rounded-lg 
                bg-cyan-500 hover:bg-cyan-600 
                transition duration-300 font-semibold shadow-md">
                Sign In
            </button>
        </form>

        <!-- Footer -->
        <p class="text-center text-sm text-white/60 mt-6">
            Don’t have an account?
            <a href="{{ route('register') }}" class="text-cyan-400 hover:underline">
                Create Account
            </a>
        </p>

    </div>

</body>
</html>