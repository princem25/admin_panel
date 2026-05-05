<x-app-layout>
    <div class="min-h-screen py-10 px-4">
        <div class="max-w-4xl mx-auto space-y-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-extrabold text-gray-800 dark:text-white">All Notifications 🔔</h1>
                @if($notifications->count() > 0)
                    <button onclick="markAllAsRead()" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-full font-bold text-sm transition shadow-lg">
                        Mark All as Read
                    </button>
                @endif
            </div>

            <div class="bg-white dark:bg-white/10 shadow-xl rounded-3xl overflow-hidden border border-gray-200 dark:border-white/20">
                <div class="divide-y divide-gray-100 dark:divide-white/5">
                    @forelse($notifications as $notification)
                        <div class="p-6 transition hover:bg-gray-50 dark:hover:bg-white/5 {{ $notification->unread() ? 'bg-blue-50/30 dark:bg-blue-500/5 border-l-4 border-blue-500' : '' }}">
                            <div class="flex items-start gap-6">
                                <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-white/10 flex items-center justify-center text-2xl shadow-sm">
                                    @php $icon = $notification->data['icon'] ?? 'bell'; @endphp
                                    @if($icon == 'truck') 🚚 @elseif($icon == 'package') 📦 @else 🔔 @endif
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between gap-4">
                                        <p class="text-lg font-bold text-gray-800 dark:text-white">
                                            {{ $notification->data['message'] ?? 'New Notification' }}
                                        </p>
                                        <span class="text-xs font-medium text-gray-400 uppercase tracking-widest">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                    
                                    @if(isset($notification->data['url']))
                                        <div class="mt-4 flex items-center gap-4">
                                            <button onclick="markAsRead('{{ $notification->id }}')" class="px-4 py-2 bg-gray-100 dark:bg-white/10 hover:bg-gray-200 dark:hover:bg-white/20 text-gray-700 dark:text-white rounded-xl text-xs font-bold transition">
                                                View Details
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-20 text-center">
                            <div class="text-6xl mb-4">📭</div>
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white">All caught up!</h3>
                            <p class="text-gray-500 dark:text-white/40 mt-2">You have no new notifications.</p>
                        </div>
                    @endforelse
                </div>

                @if($notifications->hasPages())
                    <div class="p-6 bg-gray-50 dark:bg-white/5 border-t border-gray-100 dark:border-white/5">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
