<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Support Ticket #') }}{{ $supportTicket->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-6 flex justify-between items-center">
                        <a href="{{ route('support.index') }}" class="text-indigo-600 hover:text-indigo-900 font-semibold">&larr; Back to List</a>
                        
                        @if(auth()->user()->role === 'admin' && $supportTicket->status !== 'closed')
                            <a href="{{ route('admin.support.edit', $supportTicket) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg transition duration-150 ease-in-out shadow-sm">
                                Edit / Update
                            </a>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Ticket Details</h3>
                            <p class="mb-2"><strong>Subject:</strong> {{ $supportTicket->subject }}</p>
                            <p class="mb-2"><strong>Customer:</strong> {{ $supportTicket->customer_name }}</p>
                            <p class="mb-2"><strong>Created:</strong> {{ $supportTicket->created_at->format('M d, Y H:i') }}</p>
                            <p class="mb-2"><strong>Status:</strong> 
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($supportTicket->status === 'open') bg-blue-100 text-blue-800 
                                    @elseif($supportTicket->status === 'in_progress') bg-purple-100 text-purple-800 
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $supportTicket->status)) }}
                                </span>
                            </p>
                            <p class="mb-2"><strong>Priority:</strong> 
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($supportTicket->priority === 'high') bg-red-100 text-red-800 
                                    @elseif($supportTicket->priority === 'medium') bg-yellow-100 text-yellow-800 
                                    @else bg-green-100 text-green-800 @endif">
                                    {{ ucfirst($supportTicket->priority) }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Message</h3>
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                {{ $supportTicket->message ?? 'No message provided.' }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Admin Response</h3>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            {{ $supportTicket->admin_comment ?? 'No response yet.' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
