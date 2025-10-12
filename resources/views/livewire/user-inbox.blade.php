 <div>
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <h1 class="text-2xl md:text-4xl font-bold text-red-900">My ACM Requests</h1>
    
    <flux:modal.trigger name="archive">
        <flux:button class="!bg-red-800 !text-white">Archive</flux:button>
    </flux:modal.trigger>
 </div>
 <div class="mt-6 p-4 bg-white rounded-xl shadow">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm md:text-base divide-y divide-gray-200">
            <thead class="bg-amber-100">
                <tr>
                    <th class="px-3 md:px-6 py-3 text-left font-bold text-gray-700 uppercase">Title</th>
                    <th class="px-3 md:px-6 py-3 text-left font-bold text-gray-700 uppercase">Request Purpose</th>
                    <th class="px-3 md:px-6 py-3 text-left font-bold text-gray-700 uppercase">Status</th>
                    <th class="px-3 md:px-6 py-3 text-right font-bold text-gray-700 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($requests as $request)
                    <tr>
                        <td class="px-3 md:px-6 py-4 whitespace-nowrap">{{ $request->researchProject->title }}</td>
                        <td class="px-3 md:px-6 py-4 whitespace-nowrap">{{ $request->purpose }}</td>
                        <td class="px-3 md:px-6 py-4 whitespace-nowrap">{{ $request->status }}</td>
                        <td class="px-3 md:px-6 py-4 whitespace-nowrap text-right space-x-2">
                            @if($request->status === 'pending')
                                <flux:button variant="danger" icon="x-mark" size="sm">Cancel</flux:button>
                            @else
                                <flux:button 
                                    icon="folder-arrow-down" 
                                    size="sm"
                                    wire:click="downloadRequest({{ $request->id }})"
                                    {{-- href="{{ asset(str_replace('public/', 'storage/', $request->pdf_path)) }}" target="_blank" --}}
                                    >
                                    Download
                                </flux:button>
                                <flux:button variant="danger" icon="x-mark" size="sm"
                                    wire:click="deleteRequest({{ $request->id }})">Delete</flux:button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
 </div>
    <flux:modal name="archive" class="max-w-5xl">

    
    <flux:heading class="text-red-900 text-2xl md:text-2xl ">Archive</flux:heading>
    <br>
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="lg:w-full text-sm md:text-base divide-y divide-gray-200">
            <thead class="bg-amber-100">
                <tr>
                    <th class="px-3 md:px-6 py-3 text-left font-bold text-gray-700 uppercase tracking-wider">
                        Title
                    </th>
                    <th class="px-3 md:px-6 py-3 text-left font-bold text-gray-700 uppercase tracking-wider">
                        Request Purpose
                    </th>
                    <th class="px-3 md:px-6 py-3 text-left font-bold text-gray-700 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-3 md:px-6 py-3 text-right font-bold text-gray-700 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($archives as $archive)
                    <tr>
                        <td class="px-3 md:px-6 py-4 whitespace-nowrap text-gray-700">
                            {{ $archive->researchProject->title }}
                        </td>
                        <td class="px-3 md:px-6 py-4 whitespace-nowrap text-gray-700">
                            {{ $archive->purpose }}
                        </td>
                        <td class="px-3 md:px-6 py-4 whitespace-nowrap text-gray-700">
                            {{ $archive->status }}
                        </td>
                        <td class="px-3 md:px-6 py-4 whitespace-nowrap text-right">
                            <flux:button 
                                variant="danger" 
                                icon="arrow-path" 
                                size="sm" 
                                wire:click="restoreRequest({{ $archive->id }})">
                                Restore
                            </flux:button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-3 md:px-6 py-4 text-center text-gray-500">
                            No archived requests.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</flux:modal>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('triggerDownload', (fileUrl) => {
            // Wait a bit to simulate “preparing”
            setTimeout(() => {
                const link = document.createElement('a');
                link.href = fileUrl;
                link.target = '_blank';
                link.download = '';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }, 1200); // small delay to show toast
        });
    });
</script>

</div>

