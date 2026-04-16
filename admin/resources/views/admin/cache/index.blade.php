<x-app-layout>

    <div class="min-h-screen relative overflow-hidden
        bg-white text-gray-900
        dark:bg-gradient-to-br dark:from-[#0f2027] dark:via-[#203a43] dark:to-[#2c5364] dark:text-white">

        {{-- Glow effects (dark mode only) --}}
        <div class="hidden dark:block absolute w-[500px] h-[500px] bg-cyan-400 opacity-10 blur-3xl rounded-full top-0 left-0 pointer-events-none"></div>
        <div class="hidden dark:block absolute w-[500px] h-[500px] bg-purple-500 opacity-10 blur-3xl rounded-full bottom-0 right-0 pointer-events-none"></div>

        <div class="relative max-w-6xl mx-auto px-6 py-10">

            {{-- Page Header --}}
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold">🗄️ Cache Monitor</h1>
                    <p class="text-sm text-gray-500 dark:text-white/50 mt-1">Read-only performance overview · Redis-backed</p>
                </div>
                <a href="{{ route('admin.dashboard') }}"
                   class="px-4 py-2 rounded-lg text-sm font-medium bg-gray-100 hover:bg-gray-200 dark:bg-white/10 dark:hover:bg-white/20 transition">
                    ← Back to Dashboard
                </a>
            </div>


            @if(!$redisAvailable)
                <div class="mb-6 px-5 py-4 rounded-xl border border-red-300 bg-red-50 dark:bg-red-900/30 dark:border-red-700 text-red-800 dark:text-red-300 flex items-center gap-3">
                    <span class="text-xl">⚠️</span>
                    <span class="font-medium">Redis is not reachable. Some metrics are unavailable.</span>
                </div>
            @endif

            {{-- ── Top Stats Row ── --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">

                {{-- Total Keys --}}
                <div class="p-5 rounded-2xl shadow border flex flex-col gap-1
                            bg-white border-gray-200
                            dark:bg-white/10 dark:backdrop-blur-xl dark:border-white/20">
                    <span class="text-2xl">🔑</span>
                    <p class="text-xs font-semibold text-gray-500 dark:text-white/50 uppercase tracking-wide">Total Keys</p>
                    <p class="text-3xl font-bold">{{ $totalKeys }}</p>
                </div>

                {{-- Memory Used --}}
                <div class="p-5 rounded-2xl shadow border flex flex-col gap-1
                            bg-white border-gray-200
                            dark:bg-white/10 dark:backdrop-blur-xl dark:border-white/20">
                    <span class="text-2xl">💾</span>
                    <p class="text-xs font-semibold text-gray-500 dark:text-white/50 uppercase tracking-wide">Memory Used</p>
                    <p class="text-3xl font-bold">{{ $usedMemory }}</p>
                    <p class="text-xs text-gray-400 dark:text-white/40">Peak: {{ $peakMemory }}</p>
                </div>

                {{-- Hit Rate --}}
                <div class="p-5 rounded-2xl shadow border flex flex-col gap-1
                            bg-white border-gray-200
                            dark:bg-white/10 dark:backdrop-blur-xl dark:border-white/20">
                    <span class="text-2xl">🎯</span>
                    <p class="text-xs font-semibold text-gray-500 dark:text-white/50 uppercase tracking-wide">Hit Rate</p>
                    @if($hitRate !== null)
                        <p class="text-3xl font-bold {{ $hitRate >= 80 ? 'text-green-600 dark:text-green-400' : ($hitRate >= 50 ? 'text-yellow-500' : 'text-red-500') }}">
                            {{ $hitRate }}%
                        </p>
                        <p class="text-xs text-gray-400 dark:text-white/40">Hits: {{ number_format($hitCount) }} / Misses: {{ number_format($missCount) }}</p>
                    @else
                        <p class="text-sm text-gray-400 dark:text-white/40 italic mt-1">Not available</p>
                        <p class="text-xs text-gray-400 dark:text-white/30">Requires keyspace stats enabled</p>
                    @endif
                </div>

                {{-- Redis Uptime --}}
                <div class="p-5 rounded-2xl shadow border flex flex-col gap-1
                            bg-white border-gray-200
                            dark:bg-white/10 dark:backdrop-blur-xl dark:border-white/20">
                    <span class="text-2xl">⏱️</span>
                    <p class="text-xs font-semibold text-gray-500 dark:text-white/50 uppercase tracking-wide">Redis Uptime</p>
                    <p class="text-3xl font-bold">{{ $uptimedays }}<span class="text-base font-normal text-gray-400 dark:text-white/40"> days</span></p>
                </div>

            </div>

            {{-- ── Singleton Key Status ── --}}
            <div class="mb-8">
                <h2 class="text-lg font-bold mb-3">📍 Key Status Check</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($singletonChecks as $key => $exists)
                        <div class="flex items-center justify-between px-4 py-3 rounded-xl border shadow-sm
                                    bg-white border-gray-200
                                    dark:bg-white/10 dark:backdrop-blur-xl dark:border-white/20">
                            <span class="font-mono text-sm text-gray-700 dark:text-white/80">{{ $key }}</span>
                            @if($exists)
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400">HIT</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-600 dark:bg-red-500/20 dark:text-red-400">MISS</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ── Cache Tag Groups ── --}}
            <div class="mb-8">
                <h2 class="text-lg font-bold mb-4">🏷️ Cache Tag Registry</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    @foreach($knownCacheGroups as $group)
                        @php
                            $colours = [
                                'blue'   => 'border-blue-300 dark:border-blue-600/50',
                                'purple' => 'border-purple-300 dark:border-purple-600/50',
                                'green'  => 'border-green-300 dark:border-green-600/50',
                            ];
                            $badges = [
                                'blue'   => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400',
                                'purple' => 'bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-400',
                                'green'  => 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400',
                            ];
                        @endphp
                        <div class="rounded-2xl border shadow p-5
                                    bg-white dark:bg-white/10 dark:backdrop-blur-xl
                                    {{ $colours[$group['color']] }}">

                            <div class="flex items-center gap-3 mb-4">
                                <span class="text-2xl">{{ $group['icon'] }}</span>
                                <div>
                                    <h3 class="font-bold text-base">Tag: <code class="font-mono {{ $badges[$group['color']] }} px-2 py-0.5 rounded text-sm">{{ $group['tag'] }}</code></h3>
                                    <p class="text-xs text-gray-400 dark:text-white/40 mt-0.5">TTL: {{ $group['ttl'] }}</p>
                                </div>
                            </div>

                            <ul class="space-y-2">
                                @foreach($group['keys'] as $key)
                                    <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-white/70">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400 dark:bg-white/40 flex-shrink-0"></span>
                                        <code class="font-mono text-xs break-all">{{ $key }}</code>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ── Clear Cache Action ── --}}
            <div class="rounded-2xl border shadow p-6
                        bg-white border-gray-200
                        dark:bg-white/10 dark:backdrop-blur-xl dark:border-white/20">

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-bold text-red-600 dark:text-red-400 flex items-center gap-2">
                            🧹 Flush All Cache
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-white/50 mt-1">
                            Clears all cached data across all tags. Pages will reload from the database until cache warms up again.
                        </p>
                    </div>

                    <form
                        method="POST"
                        action="{{ route('admin.cache.flush') }}"
                        onsubmit="return confirm('Are you sure you want to flush all cache? This will temporarily increase database load.');">
                        @csrf
                        <button id="flush-btn" type="submit"
                            class="px-6 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-semibold shadow transition whitespace-nowrap">
                            🗑️ Clear All Cache
                        </button>
                    </form>
                </div>
            </div>

            <p class="mt-6 text-xs text-center text-gray-400 dark:text-white/30">
                This monitor is read-only. No cache logic, logging, or business operations are modified.
            </p>

        </div>
    </div>

</x-app-layout>
