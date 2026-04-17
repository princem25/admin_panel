@php
    $type = null;
    $message = null;
    
    if (session('success')) {
        $type = 'success';
        $message = session('success');
    } elseif (session('error')) {
        $type = 'error';
        $message = session('error');
    } elseif (session('info')) {
        $type = 'info';
        $message = session('info');
    }

    $classes = [
        'success' => 'bg-green-100 text-green-700 border-green-300 dark:bg-green-300/20 dark:text-green-200 dark:border-green-300/30 font-semibold',
        'error'   => 'bg-red-100 text-red-700 border-red-300 dark:bg-red-300/20 dark:text-red-200 dark:border-red-300/30 font-semibold',
        'info'    => 'bg-blue-100 text-blue-700 border-blue-300 dark:bg-blue-300/20 dark:text-blue-200 dark:border-blue-300/30 font-semibold',
    ];
@endphp

@if($message)
    <div id="flash-message"
        class="fixed top-5 left-1/2 transform -translate-x-1/2 z-[9999]
        p-3 px-6 rounded-xl shadow-2xl border backdrop-blur-md
        transition-all duration-500 {{ $classes[$type] }} flex items-center justify-between gap-4">
        
        <div class="flex items-center gap-2">
            <span class="text-xl">
                @if($type === 'success') ✅ 
                @elseif($type === 'error') ❌ 
                @else ℹ️ @endif
            </span>
            <span class="whitespace-nowrap">{{ $message }}</span>
        </div>

        <button onclick="closeFlashMessage()" class="text-gray-400 hover:text-gray-600 dark:hover:text-white transition-colors duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <script>
        function closeFlashMessage() {
            const el = document.getElementById('flash-message');
            if (el) {
                el.style.opacity = '0';
                el.style.transform = 'translate(-50%, -20px)';
                setTimeout(() => el.remove(), 500);
            }
        }

        // Auto-hide after 3 seconds
        setTimeout(closeFlashMessage, 3000);
    </script>

@endif