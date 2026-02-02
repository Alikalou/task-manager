<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Archived Tasks
        </h2>
    </x-slot>

    <div class="py-6 max-w-2xl mx-auto space-y-6">

        <div class="space-y-4">
            <h3 class="font-semibold text-gray-900">
                Archived tasks
            </h3>

            @forelse($tasks as $task)
                <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                    <div class="flex justify-between items-start gap-4">
                        <div class="space-y-1">
                            <p class="font-medium text-gray-800">
                                {{ $task->title }}
                            </p>

                            <p class="text-sm text-gray-500">
                                Completed at:
                                {{ $task->done_at ? $task->done_at->format('M d, Y') : '—' }}
                            </p>

                            <p class="text-sm text-gray-500">
                                Archived at:
                                {{ $task->archived_at ? $task->archived_at->format('M d, Y') : '—' }}
                            </p>
                        </div>

                        <div class="shrink-0">
                            <form method="POST" action="{{ route('archived-tasks.uncomplete', $task) }}"
                                onsubmit="return confirm('Change task state?');" class="flex items-center gap-2">
                                @csrf
                                @method('PATCH')

                                <select name="status" required
                                    class="text-sm border-gray-300 rounded-md focus:ring focus:ring-gray-200">
                                    <option value="" disabled selected>
                                        Move to…
                                    </option>
                                    <option value="todo">To do</option>
                                    <option value="in_progress">In progress</option>
                                </select>

                                <button type="submit"
                                    class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md
                                     bg-white border border-gray-300 text-gray-700 hover:bg-gray-100">
                                    Update
                                </button>
                            </form>
                        </div>


                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">
                    No archived tasks yet.
                </p>
            @endforelse
        </div>

    </div>
</x-app-layout>
