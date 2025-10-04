<div>
    {{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}
    <div class="flex items-center justify-between">
        <h1 class="text-4xl font-bold text-red-900">Manage Research Projects</h1>
        <div class="flex justify-end w-lg gap-2">
            <flux:input icon="magnifying-glass" placeholder="Search title, keywords or author" wire:model.live="search"  />
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
                            File
                        </th>
                        <th class="px-6 py-3 text-right text-sm font-bold text-gray-700 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($projects as $project)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $project->title }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                               @php
                                    $authors = json_decode($project->author, true);
                                    $formattedAuthors = [];

                                    if (!empty($authors) && is_array($authors)) {
                                        $formattedAuthors = array_map(function ($author) {
                                            $names = explode(' ', trim($author['name'] ?? ''));
                                            $last = array_pop($names); // get last name
                                            $initials = '';

                                            foreach ($names as $n) {
                                                if ($n !== '') {
                                                    $initials .= strtoupper($n[0]) . '.';
                                                }
                                            }

                                            return trim($initials . ' ' . $last);
                                        }, $authors);
                                    }
                                @endphp

                                {{ !empty($formattedAuthors) ? implode(', ', $formattedAuthors) : 'N/A' }}

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
                                <flux:modal.trigger name="edit-project">
                                <flux:button icon="pencil-square" size="sm" class="mr-2 size-4"/>
                                </flux:modal.trigger> 

                                <flux:modal.trigger name="delete-project">
                                <flux:button variant="danger" icon="trash" size="sm" class="size-4"/>
                                </flux:modal.trigger> 
                            </td>
                        </tr>
                    @endforeach
                
                        {{-- <tr>
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
                                 <flux:modal.trigger name="edit-project">
                                <flux:button icon="pencil-square" size="sm" class="mr-2"/>
                                </flux:modal.trigger> 
                                <flux:modal.trigger name="delete-project">
                                <flux:button variant="danger" icon="trash" size="sm" />
                                </flux:modal.trigger>   
                            </td>
                        </tr> --}}
                    
                </tbody>
            </table>
        </div>

@if($downloadLink)
    <a href="{{ $downloadLink }}" target="_blank" class="btn">Download AI PDF</a>
@endif

    </div>

    <!-- create project -->
    <flux:modal name="create-project" class="md:w-96">
        <form wire:submit.prevent="createProject" enctype="multipart/form-data">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Create new research project</flux:heading>
                    <flux:text class="mt-2">
                        Create new research project that user can download
                    </flux:text>
                </div>
                <div class="grid grid-cols-[1fr_auto] gap-2">

                
                <flux:input label="Title" placeholder="Title" wire:model="title" />
                @php
                    $currentYear = \Carbon\Carbon::now()->year;
                    $years = range($currentYear, 1900); // descending order
                @endphp
                <flux:select label="Year" wire:model="year">
                    <option value="">Select year</option>
                    @foreach($years as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </flux:select>
                </div>
                {{-- <flux:input label="Author" placeholder="Author" wire:model="author" /> --}}

                {{-- <flux:input label="Year" placeholder="Year" wire:model="year" /> --}}
                
                {{-- PDF Upload --}}
                <flux:input 
                    label="Author File" 
                    type="file" 
                    accept="application/pdf" 
                    wire:model="authorFile" 
                />
                @if($authorFile)
                    <div>Success</div>
                @endif

                <flux:input 
                    label="Capstone File" 
                    type="file" 
                    accept="application/pdf" 
                    wire:model="projectFile" 
                />


                
                @if($projectFile)
                    <div>Success</div>
                @endif

                <div class="flex">
                    <flux:spacer />
                    <flux:button type="submit" variant="primary">Create</flux:button>
                </div>
            </div>
        </form>
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
            <flux:button type="submit" variant="primary">Update</flux:button>
        </div>
    </div>
    </flux:modal>
    
     <flux:modal name="delete-project" class="md:w-96">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Delete research project</flux:heading>
            <flux:text class="mt-2">This action can't be undone</flux:text>
        </div>
        <div class="flex">
            <flux:spacer />
            <flux:button type="submit" variant="primary">Cancel</flux:button>
            <flux:button type="submit" variant="danger">Delete</flux:button>
        </div>
    </div>
    </flux:modal>
</div>
