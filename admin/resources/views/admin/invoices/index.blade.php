<x-app-layout>
    <div class="min-h-screen py-10 px-4">
        <div class="max-w-7xl mx-auto space-y-6">

            {{-- HEADER --}}
            <div class="bg-white dark:bg-white/10 shadow-xl rounded-3xl p-8 border border-gray-200 dark:border-white/20">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <h2 class="text-3xl font-extrabold text-gray-800 dark:text-white flex items-center gap-3">
                            Invoice Management 📄
                        </h2>
                        <p class="text-gray-500 dark:text-white/60 text-sm mt-1">Manage and download customer invoices.</p>
                    </div>

                    <!-- Search Form -->
                    <form method="GET" action="{{ route('admin.invoices.index') }}" class="flex gap-2">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Invoice # or name..."
                            class="rounded-2xl border-gray-200 dark:border-white/10 dark:bg-white/5 dark:text-white text-sm focus:ring-blue-500 focus:border-blue-500 w-64 h-12 px-4">
                        <button type="submit" class="px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl transition duration-200 shadow-lg shadow-blue-600/20 active:scale-95 h-12 flex items-center justify-center">
                            Search
                        </button>
                        @if(request('search'))
                            <a href="{{ route('admin.invoices.index') }}" class="px-5 py-3 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold rounded-2xl transition duration-200 h-12 flex items-center justify-center">
                                Clear
                            </a>
                        @endif
                    </form>
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

                @if(session('info'))
                    <div class="mt-4 bg-blue-100 border border-blue-400 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400 dark:border-blue-500/30 px-4 py-3 rounded-xl">
                        {{ session('info') }}
                    </div>
                @endif
            </div>

            {{-- INVOICES TABLE --}}
            <div class="bg-white dark:bg-white/10 shadow-xl rounded-3xl overflow-hidden border border-gray-200 dark:border-white/20">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-white/5 border-b border-gray-200 dark:border-white/10 text-gray-500 dark:text-white/70 text-xs font-bold uppercase tracking-wider">
                                <th class="p-6">Invoice Number</th>
                                <th class="p-6">Order Details</th>
                                <th class="p-6">Customer</th>
                                <th class="p-6">File Status</th>
                                <th class="p-6 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                            @forelse($invoices as $invoice)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition group">
                                    <td class="p-6">
                                        <span class="font-bold text-gray-800 dark:text-white">{{ $invoice->invoice_number }}</span>
                                    </td>
                                    <td class="p-6">
                                        <a href="{{ route('admin.orders.show', $invoice->order_id) }}" class="font-bold text-blue-600 dark:text-blue-400 hover:underline">
                                            #{{ $invoice->order_id }}
                                        </a>
                                    </td>
                                    <td class="p-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold">
                                                {{ strtoupper(substr($invoice->user->name ?? '?', 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-gray-800 dark:text-white">{{ $invoice->user->name ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-6">
                                        @if($invoice->file_size)
                                            <p class="text-sm font-bold text-gray-800 dark:text-white">{{ human_file_size($invoice->file_size ) }}</p>
                                            <p class="text-xs text-gray-500 dark:text-white/60">Updated: {{ \Carbon\Carbon::createFromTimestamp($invoice->last_modified)->format('d M, Y h:i A') }}</p>
                                        @else
                                            <span class="px-3 py-1 text-xs font-bold rounded-full border bg-yellow-100 text-yellow-800 border-yellow-200 dark:bg-yellow-500/20 dark:text-yellow-400 dark:border-yellow-500/30">
                                                Pending Generation
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-6 text-center">
                                        <div class="flex flex-col items-center gap-1">
                                            <a href="{{ $invoice->downloadUrl }}" 
                                                class="no-transition inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl transition text-sm font-bold shadow-md transform hover:-translate-y-1 active:scale-95">
                                               ⬇️ Download
                                            </a>
                                            <span class="text-[10px] text-gray-500">Link expires in 10 mins</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-20 text-center text-gray-500 dark:text-white/60">
                                        <div class="text-5xl mb-4">📭</div>
                                        No invoices generated yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($invoices->hasPages())
                    <div class="p-6 border-t border-gray-200 dark:border-white/10">
                        {{ $invoices->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
