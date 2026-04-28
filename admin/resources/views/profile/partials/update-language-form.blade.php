<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Language Preference') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your preferred language for the application.") }}
        </p>
    </header>

    <div class="mt-6 space-y-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('language.switch', 'en') }}" 
               class="px-4 py-2 rounded-lg border {{ app()->getLocale() == 'en' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600' }} transition font-bold uppercase tracking-widest text-xs">
                English
            </a>
            
            <a href="{{ route('language.switch', 'ar') }}" 
               class="px-4 py-2 rounded-lg border {{ app()->getLocale() == 'ar' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600' }} transition font-bold uppercase tracking-widest text-xs">
                Arabic
            </a>
        </div>
        
        @if (session('status') === 'locale-updated')
            <p
                x-data="{ show: true }"
                x-show="show"
                x-transition
                x-init="setTimeout(() => show = false, 2000)"
                class="text-sm text-gray-600 dark:text-gray-400"
            >{{ __('Saved.') }}</p>
        @endif
    </div>
</section>
