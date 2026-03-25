<!DOCTYPE html>
<html lang="en" class="dark"> <!-- remove "dark" for white theme -->
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen flex items-center justify-center 

bg-white text-gray-900
dark:bg-gradient-to-br dark:from-[#0f2027] dark:via-[#203a43] dark:to-[#2c5364]">

    <!-- Glow Background (only dark) -->
    <div class="hidden dark:block absolute w-[500px] h-[500px] bg-cyan-400 opacity-20 blur-3xl rounded-full top-10 left-10"></div>
    <div class="hidden dark:block absolute w-[500px] h-[500px] bg-blue-500 opacity-20 blur-3xl rounded-full bottom-10 right-10"></div>

    <!-- Card -->
    <div class="relative w-full max-w-sm p-8 rounded-2xl 

    bg-white border border-gray-200 shadow-lg text-gray-900
    dark:bg-white/10 dark:backdrop-blur-xl dark:border-white/20 
    dark:shadow-[0_10px_40px_rgba(0,0,0,0.6)] dark:text-white">

        <!-- Icon -->
        <div class="flex justify-center mb-4">
            <div class="w-10 h-10 rounded-full 
            border border-gray-300 
            dark:border-white/30 flex items-center justify-center">
                🔒
            </div>
        </div>

        <!-- Title -->
        <h2 class="text-2xl font-semibold text-center mb-1">
            Login
        </h2>

        <p class="text-center text-sm 
        text-gray-600 
        dark:text-white/70 mb-6">
            Enter your email and password to continue.
        </p>

        <!-- Session Status -->
        @if (session('status'))
            <div class="mb-4 text-green-500 text-sm text-center">
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

                    bg-white border border-gray-300 
                    placeholder-gray-500 text-gray-900
                    focus:outline-none focus:ring-2 focus:ring-blue-500

                    dark:bg-white/10 dark:border-white/20 
                    dark:placeholder-white/60 dark:text-white 
                    dark:focus:ring-cyan-400">

                @error('email')
                    <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <input type="password" name="password"
                    placeholder="Password"
                    class="w-full px-4 py-2 rounded-lg 

                    bg-white border border-gray-300 
                    placeholder-gray-500 text-gray-900
                    focus:outline-none focus:ring-2 focus:ring-blue-500

                    dark:bg-white/10 dark:border-white/20 
                    dark:placeholder-white/60 dark:text-white 
                    dark:focus:ring-cyan-400">

                @error('password')
                    <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Forgot Password -->
            @if (Route::has('password.request'))
                <div class="text-right text-sm">
                    <a href="{{ route('password.request') }}" 
                       class="text-blue-600 hover:underline dark:text-cyan-400">
                        Forgot Password?
                    </a>
                </div>
            @endif

            <!-- Button -->
            <button type="submit"
                class="w-full py-2 rounded-lg font-semibold shadow-md transition duration-300

                bg-blue-600 hover:bg-blue-700 text-white

                dark:bg-cyan-500 dark:hover:bg-cyan-600">
                Sign In
            </button>
        </form>

        <!-- Footer -->
        <p class="text-center text-sm 
        text-gray-600 
        dark:text-white/60 mt-6">
            Don’t have an account?
            <a href="{{ route('register') }}" 
               class="text-blue-600 hover:underline dark:text-cyan-400">
                Create Account
            </a>
        </p>

    </div>

</body>
</html>