<div>
    {{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}
    <div class="flex items-center justify-between">
        <h1 class="text-4xl font-bold text-red-900">Manage Research Projects</h1>
        <div class="flex justify-end w-lg">
            <flux:modal.trigger name="create-project">
            <flux:button icon="plus" class="cursor-pointer">Create Project</flux:button>
            </flux:modal.trigger>
        </div>
    </div>
    <div class="mt-6 p-4 bg-white rounded-xl">
        <div class="overflow-x-auto bg-white shadow-md rounded-xl">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-amber-100">
                    <tr class="">
                        <th class="px-6 py-3 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">
                            Title
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">
                            Author
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">
                            Year
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">
                            Uploaded At
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">
                            File
                        </th>
                        <th class="px-6 py-3 text-right text-sm font-bold text-gray-700 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    {{-- @foreach($projects as $project)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $project->title }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $project->author }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $project->year }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                @if($project->file)
                                    <a href="{{ Storage::url($project->file) }}" class="text-blue-600 underline" target="_blank">View File</a>
                                @else
                                    <span class="text-gray-400">No file</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <flux:button icon="pencil-square" size="sm" class="mr-2 size-4"/>
                                <flux:button variant="danger" icon="trash" size="sm" class="size-4"/>
                            </td>
                        </tr>
                    @endforeach --}}
                
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                Project ni leeann
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                L. Lascano, J. Bernardo, R. Aniciete 
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                2025
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                2025-09-26 18:00:26
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 underline">
                               View
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <flux:button icon="pencil-square" size="sm" class="mr-2"/>
                                <flux:button variant="danger" icon="trash" size="sm" />
                            </td>
                        </tr>
                    
                </tbody>
            </table>
        </div>
    </div>

    <!-- create project -->
    <flux:modal name="create-project" class="md:w-96">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Create new research project</flux:heading>
            <flux:text class="mt-2">Create new research project that user can download</flux:text>
        </div>
        <flux:input label="Title" placeholder="Title" />
        <flux:input label="Author" placeholder="Author" />
        <flux:input label="Year" placeholder="Year" />
        <flux:input label="File" placeholder="File" />
        <div class="flex">
            <flux:spacer />
            <flux:button type="submit" variant="primary">Create</flux:button>
        </div>
    </div>
    </flux:modal>

   <!-- edit project -->
    <flux:modal name="edit-project" class="md:w-96">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Update research project</flux:heading>
            <flux:text class="mt-2">Update research project that user can download</flux:text>
        </div>
        <flux:input label="Title" placeholder="Title" />
        <flux:input label="Author" placeholder="Author" />
        <flux:input label="Year" placeholder="Year" />
        <flux:input label="File" placeholder="File" />
        <div class="flex">
            <flux:spacer />
            <flux:button type="submit" variant="primary">Create</flux:button>
        </div>
    </div>
    </flux:modal>
</div>
