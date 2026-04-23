<x-app-layout>
    <div class="min-h-screen py-10 px-4">
        <div class="max-w-7xl mx-auto space-y-6">

            {{-- HEADER --}}
            <div class="bg-white dark:bg-white/10 shadow-xl rounded-3xl p-8 border border-gray-200 dark:border-white/20">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <h2 class="text-3xl font-extrabold text-gray-800 dark:text-white flex items-center gap-3">
                            File Manager 📁
                        </h2>
                        <p class="text-gray-500 dark:text-white/60 text-sm mt-1">Manage, archive, and clean up system report files.</p>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <form action="{{ route('admin.reports.cleanup') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete all reports older than 30 days? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="no-transition px-5 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold text-sm transition shadow-lg flex items-center gap-2 transform hover:-translate-y-1 active:scale-95">
                                🧹 Bulk Cleanup (30+ Days)
                            </button>
                        </form>
                    </div>
                </div>

                @if(session('success'))
                    <div class="mt-4 bg-green-100 border border-green-400 text-green-700 dark:bg-green-500/20 dark:text-green-400 dark:border-green-500/30 px-4 py-3 rounded-xl">
                        {{ session('success') }}
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="mt-4 bg-red-100 border border-red-400 text-red-700 dark:bg-red-500/20 dark:text-red-400 dark:border-red-500/30 px-4 py-3 rounded-xl">
                        {{ session('error') }}
                    </div>
                @endif
                
                @if(session('warning'))
                    <div class="mt-4 bg-yellow-100 border border-yellow-400 text-yellow-800 dark:bg-yellow-500/20 dark:text-yellow-400 dark:border-yellow-500/30 px-4 py-3 rounded-xl">
                        {{ session('warning') }}
                    </div>
                @endif
            </div>

            {{-- FILES TABLE --}}
            <div class="bg-white dark:bg-white/10 shadow-xl rounded-3xl overflow-hidden border border-gray-200 dark:border-white/20">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-white/5 border-b border-gray-200 dark:border-white/10 text-gray-500 dark:text-white/70 text-xs font-bold uppercase tracking-wider">
                                <th class="p-6">File Name</th>
                                <th class="p-6">File Size</th>
                                <th class="p-6">Last Modified</th>
                                <th class="p-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                            @forelse($reportFiles as $file)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition group">
                                    <td class="p-6">
                                        <div class="flex items-center gap-3">
                                            <div class="text-2xl">
                                                @if(Str::endsWith($file->name, '.pdf')) 📕
                                                @elseif(Str::endsWith($file->name, '.csv')) 📊
                                                @else 📄
                                                @endif
                                            </div>
                                            <span class="font-bold text-gray-800 dark:text-white">{{ $file->name }}</span>
                                        </div>
                                    </td>
                                    <td class="p-6">
                                        <span class="text-sm font-medium text-gray-800 dark:text-white">{{ $file->size_kb }} KB</span>
                                    </td>
                                    <td class="p-6">
                                        <span class="text-sm text-gray-600 dark:text-white/80">{{ $file->last_modified }}</span>
                                    </td>
                                    <td class="p-6 text-right">
                                        <form action="{{ route('admin.reports.archive', $file->name) }}" method="POST" class="inline-block" onsubmit="return confirm('Archive this file? It will be moved to the archive folder.')">
                                            @csrf
                                            <button type="submit" class="px-4 py-2 bg-gray-800 dark:bg-white/20 hover:bg-gray-900 dark:hover:bg-white/30 text-white rounded-lg text-xs font-bold shadow-sm transition">
                                                📦 Archive
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-20 text-center text-gray-500 dark:text-white/60">
                                        <div class="text-5xl mb-4">📭</div>
                                        No files found in the reports directory.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
