<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Projects
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto">
        <a href="{{ route('projects.create') }}"
           class="bg-blue-500 text-white px-4 py-2 rounded">
            + New Project
        </a>

       

        <div class="mt-6">
            @forelse ($projects as $project)
                <div class="border p-4 mb-3 rounded">
                    <h3 class="font-bold">{{ $project->name }}</h3>
                    <p class="text-gray-600">{{ $project->description }}</p>

                    <div class="mt-2 flex gap-3">
                        <a href=" {{ route('projects.show', $project) }}"
                            class="text-blue-600">See project</a>
                        <a href="{{ route('projects.edit', $project) }}"
                           class="text-blue-600">Edit</a>

                        <form action="{{ route('projects.destroy', $project) }}"
                              onsubmit="return confirm('Delete this project? This can\'t be undone.');"
                              method="POST">
                            @csrf
                            @method('DELETE')

                            <button class="text-red-600">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="border rounded p-4 text-gray-600">
                    No projects yet. Create your first project.
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
