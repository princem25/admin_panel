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
 
            {{-- Search Bar --}}
            <div class="mb-6">
                <form action="{{ route('admin.users.index') }}" method="GET" class="flex gap-4">
                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..." class="w-full pl-10 pr-4 py-3 bg-white dark:bg-white/10 dark:backdrop-blur-xl border border-gray-200 dark:border-white/20 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:text-white dark:placeholder-gray-400 transition-all">
                    </div>

                    {{-- Sort Buttons --}}
                    <div class="flex border border-gray-200 dark:border-white/20 rounded-xl overflow-hidden shadow-sm">
                        <button type="submit" name="sort" value="all" class="px-4 py-3 text-sm bg-white dark:bg-white/10 dark:text-white font-medium hover:bg-gray-50 dark:hover:bg-white/20 transition-colors {{ request('sort') === 'all' || !request('sort') ? 'bg-blue-50 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400' : '' }}">
                            All
                        </button>
                        <button type="submit" name="sort" value="latest" class="px-4 py-3 text-sm bg-white dark:bg-white/10 dark:text-white font-medium border-l border-gray-200 dark:border-white/20 hover:bg-gray-50 dark:hover:bg-white/20 transition-colors {{ request('sort') === 'latest' ? 'bg-blue-50 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400' : '' }}">
                            Latest
                        </button>
                        <button type="submit" name="sort" value="oldest" class="px-4 py-3 text-sm bg-white dark:bg-white/10 dark:text-white font-medium border-l border-gray-200 dark:border-white/20 hover:bg-gray-50 dark:hover:bg-white/20 transition-colors {{ request('sort') === 'oldest' ? 'bg-blue-50 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400' : '' }}">
                            Oldest
                        </button>
                    </div>

                    <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 dark:bg-cyan-600 dark:hover:bg-cyan-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                        Search
                    </button>
                    @if(request('search') || request('sort'))
                        <a href="{{ route('admin.users.index') }}" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-white/10 dark:hover:bg-white/20 text-gray-700 dark:text-white font-bold rounded-xl shadow-sm transition-all duration-300">
                            Clear
                        </a>
                    @endif
                </form>
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
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 text-right">Actions</th>
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
                                        <div class="flex justify-end items-center gap-2">
                                            <span class="px-3 py-1 bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400 text-xs font-bold rounded-full border border-green-200 dark:border-green-500/30">
                                                Active
                                            </span>
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this customer?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3 py-1 bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400 text-xs font-bold rounded-full border border-red-200 dark:border-red-500/30 hover:bg-red-200 dark:hover:bg-red-500/30 transition-colors">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
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
