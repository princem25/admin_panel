<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Support Tickets') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Tickets</h3>
                        @if(auth()->user()->role !== 'admin')
                            <a href="{{ route('support.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-150 ease-in-out shadow-sm">
                                Create New Ticket
                            </a>
                        @endif
                    </div>

                    @if($tickets->isEmpty())
                        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                            No support tickets found.
                        </div>
                    @else
                        <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow overflow-y-hidden">
                            <table class="w-full whitespace-no-wrap table-striped">
                                <thead>
                                    <tr class="text-left font-bold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 border-b dark:border-gray-600">
                                        <th class="px-6 py-4">ID</th>
                                        <th class="px-6 py-4">Subject</th>
                                        <th class="px-6 py-4">Customer</th>
                                        <th class="px-6 py-4">Priority</th>
                                        <th class="px-6 py-4">Status</th>
                                        <th class="px-6 py-4">Created At</th>
                                        <th class="px-6 py-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($tickets as $ticket)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                                            <td class="px-6 py-4">#{{ $ticket->id }}</td>
                                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-gray-100">{{ $ticket->subject }}</td>
                                            <td class="px-6 py-4">
                                                {{ $ticket->customer_name }}
                                                @if(auth()->user()->role === 'admin')
                                                    <span class="text-xs text-gray-500">(ID: {{ $ticket->user_id }})</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    @if($ticket->priority === 'high') bg-red-100 text-red-800 
                                                    @elseif($ticket->priority === 'medium') bg-yellow-100 text-yellow-800 
                                                    @else bg-green-100 text-green-800 @endif">
                                                    {{ ucfirst($ticket->priority) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    @if($ticket->status === 'open') bg-blue-100 text-blue-800 
                                                    @elseif($ticket->status === 'in_progress') bg-purple-100 text-purple-800 
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $ticket->created_at->format('M d, Y H:i') }}</td>
                                            <td class="px-6 py-4 text-sm">
                                                <a href="{{ route('support.show', $ticket) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold mr-2">View</a>
                                                @if(auth()->user()->role === 'admin')
                                                    @if($ticket->status !== 'closed')
                                                        <a href="{{ route('admin.support.edit', $ticket) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold">Edit</a>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
