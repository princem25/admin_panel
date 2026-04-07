<x-app-layout>
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900 py-12">
        <div class="max-w-7xl mx-auto px-6">
            
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                        Customer Management
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        View and manage all registered customers in the system.
                    </p>
                </div>
                <div class="bg-blue-600 dark:bg-cyan-600 text-white px-4 py-2 rounded-lg font-bold shadow-lg">
                    Total: {{ $users->total() }}
                </div>
            </div>

            {{-- Users Table --}}
            <div class="bg-white dark:bg-white/10 dark:backdrop-blur-xl border border-gray-200 dark:border-white/20 shadow-xl rounded-2xl overflow-hidden text-gray-900 dark:text-white">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-white/5 border-b border-gray-200 dark:border-white/10">
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Customer</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Email</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Joined Date</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                            @foreach($users as $user)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center font-bold text-white shadow-md">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-900 dark:text-white">{{ $user->name }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400 capitalize">{{ $user->role }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                        {{ $user->email }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                        {{ $user->created_at->format('M d, Y') }}
                                        <span class="block text-xs opacity-50">{{ $user->created_at->diffForHumans() }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="px-3 py-1 bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400 text-xs font-bold rounded-full border border-green-200 dark:border-green-500/30">
                                            Active
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                            
                            @if($users->isEmpty())
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                            </svg>
                                            <p>No customers found in the system.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($users->hasPages())
                    <div class="px-6 py-4 bg-gray-50 dark:bg-white/5 border-t border-gray-200 dark:border-white/10">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
