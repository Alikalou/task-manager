<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $project->name }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto space-y-4">



        {{-- Project info --}}
        <div class="border p-4 rounded space-y-3">
            <div class="text-gray-600 whitespace-pre-line">
                @if ($project->description !== null)
                    {{ "Project Description:\n" . $project->description }}
                @else
                    {{ 'No description.' }}
                @endif
            </div>

            <div class="flex gap-3">
                <a href="{{ route('projects.edit', $project) }}" class="text-blue-600">Edit</a>
                <a href="{{ route('projects.index') }}" class="text-gray-600">Back</a>
            </div>


            <h2 class="text-lg font-semibold text-gray-900">Recent Activity</h2>
            <details>
                <div class="mt-3 space-y-3">

                    @forelse($activities as $log)
                        <div class="rounded border bg-white p-3">
                            <div class="text-sm text-gray-900">
                                {{ $log->description }}
                            </div>

                            <div class="mt-1 text-xs text-gray-500 flex items-center gap-2">
                                <span>{{ $log->created_at->diffForHumans() }}</span>
                                <span>•</span>
                                <span>
                                    {{ $log->user?->name ?? 'System' }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-sm text-gray-500">No activity yet.</div>
                    @endforelse
                </div>

            </details>
        </div>


        {{-- Add task (distinct division) --}}
        <div class="border p-4 rounded space-y-3">
            @if ($errors->any())
                <div class="border rounded p-3 text-red-700">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <h3 class="font-semibold">Add a task</h3>
            <form method="POST" action="{{ route('projects.tasks.store', $project) }}"
                class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                @csrf

                <div>
                    <label class="block text-sm text-gray-600">Task name</label>
                    <input type="text" name="title" value="{{ old('title') }}" class="border rounded p-2 w-full"
                        placeholder="e.g. Task 1" required />
                </div>

                <div>
                    <label class="block text-sm text-gray-600">Due date</label>
                    <input type="date" name="due_date" value="{{ old('due_date') }}"
                        min="{{ now()->toDateString() }}" class="border rounded p-2 w-full" />
                </div>

                <div>
                    <label class="block text-sm text-gray-600">Progress</label>

                    <select name="status">
                        @foreach (App\Enums\TaskStatus::cases() as $p)
                            <option value="{{ $p->value }}" @selected(request('status') === $p->value)>
                                {{ ucfirst($p->label()) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block
                        text-sm text-gray-600">Priority</label>

                    <select name="priority">
                        @foreach (App\Enums\TaskPriority::cases() as $p)
                            <option value="{{ $p->value }}" @selected(request('priority') === $p->value)>
                                {{ ucfirst($p->value) }}
                            </option>
                        @endforeach
                    </select>
                </div>


                <div x-data="{
                    selected: @js(old('tag_ids', [])),
                    toggle(id) {
                        if (this.selected.includes(id)) {
                            this.selected = this.selected.filter(i => i !== id)
                        } else {
                            this.selected.push(id)
                        }
                    },
                    remove(id) {
                        this.selected = this.selected.filter(i => i !== id)
                    }
                }" class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Tags</label>

                    {{-- Selected tags (chips) --}}
                    <div class="flex flex-wrap gap-2">
                        <template x-for="id in selected" :key="id">
                            <span
                                class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-3 py-1 text-sm text-blue-800">
                                <span x-text="$refs['tag-'+id]?.innerText"></span>
                                <button type="button" class="text-blue-600 hover:text-blue-900" @click="remove(id)">
                                    ×
                                </button>
                                <input type="hidden" name="tag_ids[]" :value="id">
                            </span>
                        </template>

                        <span x-show="selected.length === 0" class="text-sm text-gray-500">
                            No tags selected
                        </span>
                    </div>

                    {{-- Available tags --}}
                    <div class="rounded-md border bg-white p-3 max-h-40 overflow-y-auto space-y-1">
                        @foreach ($tags as $tag)
                            <button type="button"
                                class="block w-full text-left rounded px-2 py-1 text-sm
                       hover:bg-gray-100"
                                :class="selected.includes({{ $tag->id }}) && 'bg-blue-50 text-blue-700'"
                                @click="toggle({{ $tag->id }})">
                                <span x-ref="tag-{{ $tag->id }}">{{ $tag->name }}</span>
                            </button>
                        @endforeach
                    </div>

                    @error('tag_ids')
                        <div class="text-red-600 text-sm">{{ $message }}</div>
                    @enderror
                </div>



                <div class="flex gap-2">
                    <button type="submit" class="border rounded px-4 py-2 w-full sm:w-auto">
                        Add
                    </button>
                </div>
            </form>


        </div>

        {{-- Filters (distinct division, collapsible show/hide) --}}
        <div class="border rounded">
            <details class="p-4"
                {{ request()->hasAny(['status', 'due', 'sort', 'priority_sort', 'search', 'tag_ids']) ? 'open' : '' }}>

                <summary class="cursor-pointer select-none font-semibold">
                    Filters
                    <span class="text-sm text-gray-500 font-normal">
                        (click to
                        {{ request()->hasAny(['status', 'due', 'sort', 'priority_sort', 'search', 'tag_ids']) ? 'hide' : 'show' }})

                    </span>
                </summary>

                <div class="mt-4">
                    <form method="GET" action="{{ route('projects.show', $project) }}"
                        class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">

                        <div>
                            <label class="block text-sm text-gray-600">Status</label>
                            <select name="status">

                                @foreach (App\Enums\TaskStatus::cases() as $p)
                                    <option value="{{ $p->value }}" @selected(request('status') === $p->value)>
                                        {{ ucfirst($p->label()) }}
                                    </option>
                                @endforeach
                            </select>

                        </div>

                        <div>
                            <label class="block text-sm text-gray-600">Due</label>
                            <select name="due" class="border rounded px-3 py-2 w-full">
                                <option value="">Any</option>
                                <option value="overdue" @selected(request('due') === 'overdue')>Overdue</option>
                                <option value="today" @selected(request('due') === 'today')>Today</option>
                                <option value="this_week" @selected(request('due') === 'this_week')>This week</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm text-gray-600">Sort</label>
                            <select name="sort" class="border rounded px-3 py-2 w-full">
                                <option value="newest" @selected(request('sort', 'newest') === 'newest')>Newest</option>
                                <option value="oldest" @selected(request('sort') === 'oldest')>Oldest</option>
                                <option value="due_asc" @selected(request('sort') === 'due_asc')>Due date ↑</option>
                                <option value="due_desc" @selected(request('sort') === 'due_desc')>Due date ↓</option>

                            </select>
                        </div>

                        <div>
                            <label class="block text-sm text-gray-600">Sort by priority</label>
                            <select name="priority_sort" class="border rounded px-3 py-2 w-full">
                                <option value="" @selected(!request()->filled('priority_sort') === null)>None</option>
                                <option value="first" @selected(request('priority_sort') === 'first')>Priority first</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm text-gray-600">Search</label>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="e.g. invoice" class="border rounded px-3 py-2 w-full" />
                        </div>

                        <div>
                            <label class="block text-sm text-gray-600">Tags</label>

                            <select name="tag_ids[]" multiple class="border rounded px-3 py-2 w-full h-32">
                                @foreach ($tags as $tag)
                                    <option value="{{ $tag->id }}" @selected(in_array($tag->id, old('tag_ids', $selectedTagIds ?? [])))>
                                        {{ $tag->name }}
                                    </option>
                                @endforeach
                            </select>

                            <p class="text-xs text-gray-500 mt-1">
                                Hold Ctrl (Cmd on Mac) to select multiple
                            </p>
                        </div>

                        <div class="sm:col-span-4 flex gap-3">
                            <button class="border rounded px-4 py-2">Apply</button>
                            <a href="{{ route('projects.show', $project) }}"
                                class="text-gray-600 underline self-center">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </details>
        </div>

        {{-- Task list (distinct division) --}}

        <div class="flex items-center justify-between gap-4">
            {{-- Left side --}}
            <div class="flex items-baseline gap-3">
                <h3 class="font-semibold">Tasks</h3>
                <span class="text-sm text-gray-500">
                    {{ $project->tasks->count() }} total
                </span>
            </div>

            {{-- Right side --}}

            <form id="bulkDeleteForm" method="POST" action="{{ route('projects.tasks.bulkDestroy', $project) }}"
                onsubmit="return confirm('Delete selected tasks?')" class="hidden">
                @csrf
                @method('DELETE')
            </form>

            <div class="flex items-center gap-3">
                {{-- Select all --}}
                <label class="text-sm text-gray-600 flex items-center gap-2 select-none">
                    <input type="checkbox" id="select_all" class="border rounded">
                    Select all
                </label>

                {{-- Trash icon --}}
                <button type="submit" form="bulkDeleteForm" id="bulk_delete_btn"
                    class="hidden text-red-600 hover:text-red-800" title="Delete selected">
                    {{-- Heroicons: Trash --}}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21
                                        c.342.052.682.107 1.022.166m-1.022-.165
                                        L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084
                                        a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0
                                        a48.108 48.108 0 00-3.478-.397m-12 .562
                                        c.34-.059.68-.114 1.022-.165m0 0
                                        a48.11 48.11 0 013.478-.397m7.5 0v-.916
                                        c0-1.18-.91-2.164-2.09-2.201
                                        a51.964 51.964 0 00-3.32 0
                                        c-1.18.037-2.09 1.022-2.09 2.201v.916" />
                    </svg>
                </button>
            </div>
        </div>


        {{-- Task list (distinct division) --}}

        <div class="divide-y">
            @forelse ($project->tasks as $task)

                <div class="py-3">
                    {{-- ROW 1: task main line --}}
                    <div class="flex items-start justify-between gap-4">

                        {{-- LEFT: checkbox + text --}}
                        <div class="flex items-start gap-3 min-w-0">
                            <div class="pt-1">
                                <input type="checkbox" class="task-checkbox border rounded" name="task_ids[]"
                                    value="{{ $task->id }}" form="bulkDeleteForm">
                            </div>

                            <div class="min-w-0 space-y-1">
                                {{-- Title --}}
                                <div class="font-semibold text-gray-900 truncate">
                                    {{ $task->title }}
                                </div>

                                {{-- Status --}}
                                <div class="text-sm text-gray-600">
                                    Status:
                                    <span
                                        class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700">
                                        {{ $task->status }}

                                    </span>
                                </div>

                                {{-- Meta row --}}
                                <div class="flex flex-wrap items-center gap-3 text-sm min-h-[1.5rem]">
                                    @if ($task->due_date)
                                        <span class="inline-flex items-center gap-1 text-gray-500">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span>{{ $task->due_date->toFormattedDateString() }}</span>
                                        </span>
                                    @endif

                                    @if ($task->priority)
                                        <span
                                            class="inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700">
                                            {{ ucfirst($task->priority->value) }}
                                        </span>
                                    @endif

                                    @php
                                        $shown = $task->tags->take(3);
                                        $rest = $task->tags->count() - $shown->count();
                                    @endphp

                                    @if ($task->tags->isNotEmpty())
                                        <div class="flex flex-wrap items-center gap-2">
                                            @foreach ($shown as $tag)
                                                <span
                                                    class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700">
                                                    {{ $tag->name }}
                                                </span>
                                            @endforeach

                                            @if ($rest > 0)
                                                <span
                                                    class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700">
                                                    +{{ $rest }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif

                                </div>
                            </div>
                        </div>

                        {{-- RIGHT: actions --}}
                        <div class="flex flex-col gap-3 shrink-0">
                            {{-- Row 1: Status + Delete --}}
                            <div class="flex items-center gap-3">
                                <form method="POST" action="{{ route('projects.tasks.update', [$project, $task]) }}"
                                    class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')

                                    <label class="text-sm text-gray-600"
                                        for="status-{{ $task->id }}">Status</label>

                                    <select id="status-{{ $task->id }}" name="status"
                                        class="rounded-md border-gray-300 bg-white text-sm px-2 py-1"
                                        onchange="this.form.submit()">
                                        @foreach (\App\Enums\TaskStatus::cases() as $s)
                                            <option value="{{ $s->value }}" @selected($task->status === $s->value)>
                                                {{ ucfirst($s->label()) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>

                                <form method="POST"
                                    action="{{ route('projects.tasks.destroy', [$project, $task]) }}"
                                    onsubmit="return confirm('Delete this task?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="text-red-600 text-sm hover:underline">
                                        Delete
                                    </button>
                                </form>
                            </div>

                            {{-- Row 2: Reminder (collapsed) --}}
                            {{-- Incremental scale: hours / minutes (no days) --}}
                            <details class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-2">

                                <summary class="cursor-pointer select-none list-none">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-semibold text-gray-800">Remind me</span>
                                        <span class="text-xs text-gray-500">click to open</span>
                                    </div>
                                </summary>
                                <form method = "POST"
                                    action = "{{ route('projects.tasks.reminder', [$project, $task]) }}"
                                    class="mt-4 flex flex-col gap-4">
                                    @csrf

                                    <div class="grid grid-cols-2 gap-3" data-reminder-scaler>
                                        {{-- Hours --}}
                                        <div class="flex flex-col gap-1">
                                            <label class="text-xs font-semibold text-gray-600"
                                                for="remind_hours-{{ $task->id }}">
                                                Hours <span class="text-red-600">*</span>
                                            </label>

                                            <div class="flex items-center gap-2">


                                                <input id="remind_hours-{{ $task->id }}" name="delay_hours"
                                                    type="number" inputmode="numeric" min="0" max="23"
                                                    value="{{ (int) old('delay_hours', 0) }}"
                                                    class="h-10 w-full rounded-md border-gray-300 bg-white text-sm px-3 py-2 text-center"
                                                    data-step-input="hours" required />


                                            </div>

                                            @error('delay_hours')
                                                <p class="text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        {{-- Minutes --}}
                                        <div class="flex flex-col gap-1">
                                            <label class="text-xs font-semibold text-gray-600"
                                                for="remind_minutes-{{ $task->id }}">
                                                Minutes <span class="text-red-600">*</span>
                                            </label>

                                            <div class="flex items-center gap-2">

                                                <input id="remind_minutes-{{ $task->id }}" name="delay_minutes"
                                                    type="number" inputmode="numeric" min="0" max="59"
                                                    step="1" value="{{ (int) old('delay_minutes', 10) }}"
                                                    class="h-10 w-full rounded-md border-gray-300 bg-white text-sm px-3 py-2 text-center"
                                                    data-step-input="minutes" required />


                                            </div>

                                            <p class="text-xs text-gray-500">Minutes step: 5</p>

                                            @error('delay_minutes')
                                                <p class="text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                    </div>
                                    {{-- Email (keep as-is) --}}
                                    <div class="flex flex-col gap-1">
                                        <label class="text-xs font-semibold text-gray-600"
                                            for="remind_email-{{ $task->id }}">
                                            Email <span class="text-red-600">*</span>
                                        </label>
                                        <input id="remind_email-{{ $task->id }}" type="email" name="email"
                                            value="{{ old('email', auth()->user()->email) }}" required
                                            class="rounded-md border-gray-300 bg-white text-sm px-3 py-3">
                                        @error('email')
                                            <p class="text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <button type="submit"
                                        class="mt-2 self-start rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-700">

                                        Schedule reminder
                                    </button>
                                </form>
                            </details>
                        </div>
                    </div>


                    {{-- ROW 2: subtasks (stacked under the main row) --}}

                    <div class="mt-3 pl-7" x-data="{ open: false }">
                        {{-- Toggle button --}}
                        <button type="button"
                            class="text-sm text-gray-600 hover:text-gray-900 inline-flex items-center gap-2"
                            @click="open = !open">
                            <span x-text="open ? 'Hide subtasks' : 'Show subtasks'"></span>
                            <span class="text-xs text-gray-400">
                                ({{ $task->subtasksProgressLabel() }})
                            </span>
                            <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" x-cloak class="mt-2 space-y-2">
                            {{-- Listing the subtasks --}}
                            @if ($task->subtasks->count())
                                <div class="space-y-1">
                                    @foreach ($task->subtasks as $subtask)
                                        <div class="flex items-center gap-2">
                                            <form method="POST"
                                                action="{{ route('projects.tasks.subtasks.update', [$project, $task, $subtask]) }}">
                                                @csrf
                                                @method('PATCH')

                                                <input type="hidden" name="is_done"
                                                    value="{{ $subtask->is_done ? 0 : 1 }}">

                                                <button type="submit"
                                                    class="text-xs border rounded px-2 py-1 {{ $subtask->is_done ? 'text-gray-600' : 'text-emerald-700' }}">
                                                    {{ $subtask->is_done ? 'Undo' : 'Done' }}
                                                </button>
                                            </form>
                                            <div
                                                class="text-sm {{ $subtask->is_done ? 'line-through text-gray-400' : 'text-gray-700' }}">
                                                {{ $subtask->title }}
                                            </div>

                                            <form method="POST"
                                                action="{{ route('projects.tasks.subtasks.destroy', [$project, $task, $subtask]) }}"
                                                class="ml-auto">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-xs text-red-600">Delete</button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-sm text-gray-500">No subtasks yet.</div>
                            @endif

                            {{-- Add subtask form --}}
                            <form method="POST"
                                action="{{ route('projects.tasks.subtasks.store', [$project, $task]) }}"
                                class="flex gap-2">
                                @csrf
                                <input name="title" class="border rounded px-2 py-1 text-sm w-full"
                                    placeholder="Add subtask..." maxlength="255" />
                                <button class="border rounded px-3 py-1 text-sm">
                                    Add
                                </button>
                            </form>
                        </div>

                    </div>
                </div>



            @empty
                <div class="py-3 text-gray-600">
                    No tasks yet. Add one to get started.
                </div>
            @endforelse
        </div>




        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const selectAll = document.getElementById('select_all');
                const bulkBtn = document.getElementById('bulk_delete_btn');

                function taskBoxes() {
                    return Array.from(document.querySelectorAll('.task-checkbox'));
                }

                function updateUI() {
                    const boxes = taskBoxes();
                    const checked = boxes.filter(cb => cb.checked).length;

                    bulkBtn.classList.toggle('hidden', checked === 0);

                    if (boxes.length === 0) {
                        selectAll.checked = false;
                        selectAll.indeterminate = false;
                        return;
                    }

                    selectAll.checked = checked === boxes.length;
                    selectAll.indeterminate = checked > 0 && checked < boxes.length;
                }

                selectAll.addEventListener('change', () => {
                    taskBoxes().forEach(cb => cb.checked = selectAll.checked);
                    updateUI();
                });

                document.addEventListener('change', (e) => {
                    if (e.target.classList.contains('task-checkbox')) {
                        updateUI();
                    }
                });

                updateUI();
            });
        </script>

</x-app-layout>
