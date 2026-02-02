<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Project
        </h2>
    </x-slot>

    <div class="py-6 max-w-xl mx-auto">
        <form method="POST"
              action="{{ route('projects.update', $project) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block">Name</label>
                <input name="name"
                       value="{{ old('name', $project->name) }}"
                       class="w-full border rounded p-2">
                @error('name')
                    <div class="text-red-600">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block">Description</label>
                <textarea name="description"
                          class="w-full border rounded p-2">{{ old('description', $project->description) }}</textarea>
            </div>

            <button class="bg-blue-500 text-white px-4 py-2 rounded">
                Update
            </button>
        </form>
    </div>
</x-app-layout>
