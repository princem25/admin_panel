@if(session('success') || session('error'))

    <div id="flash-message"
        class="fixed top-5 left-1/2 transform -translate-x-1/2 z-50
        p-3 px-6 rounded shadow-lg text-center border
        transition-opacity duration-500

        {{ session('success') 
            ? 'bg-green-100 text-green-700 border-green-300 dark:bg-green-300/20 dark:text-green-200 dark:border-green-300/30'
            : 'bg-red-100 text-red-700 border-red-300 dark:bg-red-300/20 dark:text-red-200 dark:border-red-300/30' }}">

        {{ session('success') ?? session('error') }}
    </div>

    <script>
        setTimeout(() => {
            const el = document.getElementById('flash-message');
            if (el) {
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 500);
            }
        }, 3000);
    </script>

@endif