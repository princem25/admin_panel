<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Support Ticket') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-6">
                        <a href="{{ route('support.index') }}" class="text-indigo-600 hover:text-indigo-900 font-semibold">&larr; Back to List</a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Ticket Details</h3>
                            <p class="mb-2"><strong>Subject:</strong> {{ $supportTicket->subject }}</p>
                            <p class="mb-2"><strong>Customer:</strong> {{ $supportTicket->customer_name }}</p>
                            <p class="mb-2"><strong>Created:</strong> {{ $supportTicket->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Message</h3>
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                {{ $supportTicket->message ?? 'No message provided.' }}
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('admin.support.update', $supportTicket) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                            <select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option value="open" @if($supportTicket->status === 'open') selected @endif>Open</option>
                                <option value="in_progress" @if($supportTicket->status === 'in_progress') selected @endif>In Progress</option>
                                <option value="closed" @if($supportTicket->status === 'closed') selected @endif>Closed</option>
                            </select>
                        </div>

                        <div>
                            <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Priority</label>
                            <select id="priority" name="priority" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option value="low" @if($supportTicket->priority === 'low') selected @endif>Low</option>
                                <option value="medium" @if($supportTicket->priority === 'medium') selected @endif>Medium</option>
                                <option value="high" @if($supportTicket->priority === 'high') selected @endif>High</option>
                            </select>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg transition duration-150 ease-in-out shadow-sm">
                                Update Ticket
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
