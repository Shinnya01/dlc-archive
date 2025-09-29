<div class="space-y-6">
    <!-- Header + Search -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <h1 class="text-2xl md:text-4xl font-bold text-red-900">Templates</h1>
        
        <!-- Search Area -->
        <div class="flex flex-col sm:flex-row sm:items-center gap-2 w-full md:w-auto">
            <flux:input icon="magnifying-glass" placeholder="Search Users" class="w-full sm:w-64" />
            
            <div class="flex flex-col sm:flex-row sm:items-center gap-2 w-full sm:w-auto">
                <flux:input placeholder="From" class="w-full sm:w-28" />
                <span class="hidden sm:block">-</span>
                <flux:input placeholder="To" class="w-full sm:w-28" />
            </div>

            <flux:button icon="magnifying-glass" class="w-full sm:w-auto">
                Search
            </flux:button>
        </div>
    </div>

    <!-- Templates Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($templates as $template)
        <div wire:key="{{ $template->id }}">
            <flux:modal.trigger :name="'previewFile'.$template->id">
                <button class="cursor-pointer h-auto w-full bg-white shadow-xl flex gap-4 items-center p-4 rounded-xl">
                    <flux:avatar 
                        :name="$template->title" 
                        size="sm" 
                        circle  
                        class="my-auto size-12 !bg-red-800 !text-white !text-xl"
                    />
                    <div>
                        <h2 class="text-base md:text-lg text-left font-semibold text-black">
                            {{ $template->title }}
                        </h2>
                        <p class="text-xs md:text-sm text-gray-500">
                            @php
                                $authors = json_decode($template->author, true);
                                $formattedAuthors = [];

                                if (!empty($authors) && is_array($authors)) {
                                    $formattedAuthors = array_map(function ($author) {
                                        $names = explode(' ', trim($author['name'] ?? ''));
                                        $last = array_pop($names);
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
                        </p>
                    </div>
                </button>
            </flux:modal.trigger>

            <!-- Modal -->
            <flux:modal :name="'previewFile'.$template->id" class="min-w-[90vw] md:min-w-[60vw] max-w-5xl space-y-2 cursor-not-allowed" wire:model="showModal">
                @php
                    $filePath = $template->file;
                    $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                @endphp
                <div class="h-[50vh] md:h-[60vh]">
                    @if(in_array($fileExtension, ['pdf', 'PDF']))
                        <object data="{{ Storage::url($filePath) }}" type="application/pdf" class="w-full h-full">
                            <p>PDF not supported. <a href="{{ Storage::url($filePath) }}" target="_blank">Download</a></p>
                        </object>
                    @elseif(in_array($fileExtension, ['png', 'jpg', 'jpeg', 'gif']))
                        <img src="{{ Storage::url($filePath) }}" alt="Preview" class="w-full h-full object-contain" />
                    @else
                        <p>This file type cannot be previewed. <a href="{{ Storage::url($filePath) }}" target="_blank">Download</a></p>
                    @endif
                </div>
                
                <flux:textarea placeholder="Enter Purpose of the Request" wire:model="purpose" />
                
                <div class="flex justify-end">
                    <flux:button 
                        class="!bg-red-800 !text-white" 
                        wire:click="requestACM({{ $template->id }})">
                        Request ACM
                    </flux:button>
                </div>
            </flux:modal>
        </div>
        @endforeach
    </div>
</div>
