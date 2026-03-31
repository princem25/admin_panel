<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('System Logs') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-gray-900 text-gray-200 font-mono text-xs overflow-x-auto h-[600px] overflow-y-scroll rounded-lg shadow-inner">
                    @forelse($logs as $log)
                        <div class="mb-1 pb-1 border-b border-gray-700 whitespace-nowrap">
                            @if(str_contains(strtolower($log), 'error') || str_contains(strtolower($log), 'exception'))
                                <span class="text-red-400">{{ $log }}</span>
                            @elseif(str_contains(strtolower($log), 'warning'))
                                <span class="text-yellow-400">{{ $log }}</span>
                            @elseif(str_contains(strtolower($log), 'info'))
                                <span class="text-blue-400">{{ $log }}</span>
                            @elseif(str_contains(strtolower($log), 'debug'))
                                <span class="text-gray-400">{{ $log }}</span>
                            @else
                                <span class="text-gray-200">{{ $log }}</span>
                            @endif
                        </div>
                    @empty
                        <div class="text-gray-500 italic font-sans text-center mt-10">No logs found.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
