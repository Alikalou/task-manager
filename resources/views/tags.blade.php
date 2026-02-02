<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tags
        </h2>
    </x-slot>

    <div class="py-6 max-w-2xl mx-auto space-y-6">

        {{-- ✅ Create Tag (top) --}}
        <div class="border rounded p-4 bg-white">
            <h3 class="font-semibold text-gray-900 mb-3">Create a new tag</h3>

            <form method="POST" action="{{ route('tags.store') }}" class="space-y-3">
                @csrf

                <div>
                    <label class="block text-sm text-gray-600 mb-1">Tag name</label>
                    <input name="name" value="{{ old('name') }}" class="w-full border rounded px-3 py-2"
                        placeholder="e.g. study">

                    @error('name')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <button class="bg-blue-500 text-white px-4 py-2 rounded">
                    Save
                </button>
            </form>
        </div>

        {{-- ✅ Tags List --}}
        <div class="space-y-3">
            <h3 class="font-semibold text-gray-900">Your tags</h3>

            @forelse($tags as $tag)
                <div class="border rounded p-4 bg-white">
                    <div class="flex items-start justify-between gap-4">

                        {{-- LEFT: Tag name + inline edit form --}}
                        <div class="min-w-0 flex-1">
                            {{-- Display name --}}
                            <div id="tag-view-{{ $tag->id }}">
                                <div class="font-semibold text-gray-900 truncate">
                                    {{ $tag->name }}
                                </div>
                            </div>

                            {{-- Inline edit form (hidden by default) --}}
                            <div id="tag-edit-{{ $tag->id }}" class="hidden mt-3">
                                <form method="POST" action="{{ route('tags.update', $tag) }}"
                                    class="flex items-center gap-2">
                                    @csrf
                                    @method('PUT')

                                    <input name="name" value="{{ old('name', $tag->name) }}"
                                        class="w-full border rounded px-3 py-2">

                                    <button class="bg-gray-900 text-white px-3 py-2 rounded">
                                        Update
                                    </button>

                                    <button type="button" class="border px-3 py-2 rounded"
                                        onclick="
                                            document.getElementById('tag-edit-{{ $tag->id }}').classList.add('hidden');
                                            document.getElementById('tag-view-{{ $tag->id }}').classList.remove('hidden');
                                        ">
                                        Cancel
                                    </button>
                                </form>

                                {{-- Optional: per-item edit validation message (only shows if you wire errors by id) --}}
                            </div>
                        </div>

                        {{-- RIGHT: actions --}}
                        <div class="flex items-center gap-2 shrink-0">
                            <button type="button" class="border px-3 py-2 rounded"
                                onclick="
                                    document.getElementById('tag-view-{{ $tag->id }}').classList.add('hidden');
                                    document.getElementById('tag-edit-{{ $tag->id }}').classList.remove('hidden');
                                ">
                                Edit
                            </button>

                            <form method="POST" action="{{ route('tags.destroy', $tag) }}">
                                @csrf
                                @method('DELETE')
                                <button class="border border-red-300 text-red-700 px-3 py-2 rounded"
                                    onclick="return confirm('Delete this tag?')">
                                    Delete
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            @empty
                <div class="border rounded p-4 bg-white text-gray-600">
                    No tags yet.
                </div>
            @endforelse
        </div>

    </div>
</x-app-layout>
