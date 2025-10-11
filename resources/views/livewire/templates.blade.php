<div class="space-y-6">
 
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.12.313/pdf.min.js"></script>

    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h1 class="text-2xl md:text-4xl font-bold text-red-900">Templates</h1>
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-end w-full md:w-auto">
        <flux:input icon="magnifying-glass" placeholder="Search Users" class="w-full sm:w-48" />

          <div class="flex items-center gap-2">
            <flux:input placeholder="From" class="w-24 sm:w-32" />
            <span>-</span>
            <flux:input placeholder="To" class="w-24 sm:w-32" />
        </div>

        <flux:button icon="magnifying-glass" class="w-full sm:w-auto">
            Search
        </flux:button>
    </div>
    </div>


    
    <div class="grid lg:grid-cols-3 gap-4">
        @foreach ($templates as $template)
        <div wire:key="{{ $template->id }}">
        
        <flux:modal.trigger :name="'previewFile'.$template->id">
        <button class="cursor-pointer h-auto w-full bg-white shadow-xl flex gap-4 items-center p-4 rounded-xl"
            onclick="previewPdf({{ $template->id }}, '{{ Storage::url($template->file) }}')">
            <flux:avatar :name="$template->title" size="sm" circle  class="my-auto size-12 !bg-red-800 !text-white !text-xl"/>
            <div>
                <h2 class="text-lg text-left font-semibold text-black">{{ $template->title }} </h2>
                <p class="text-sm text-gray-500">
                    {{ $template->citation }}
                    <!-- @php
                        $authors = json_decode($template->author, true);
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

                    {{ !empty($formattedAuthors) ? implode(', ', $formattedAuthors) : 'N/A' }}</p> -->
            </div>
        </button>
        </flux:modal.trigger>

            <flux:modal :name="'previewFile'.$template->id" class="min-w-[60vw] max-w-5xl space-y-2" wire:model="showModal">

               <div class="h-[60vh] overflow-auto" id="pdf-preview-container-{{ $template->id }}">
    <div id="pdf-scroll-view-{{ $template->id }}"></div>
</div>

                <flux:textarea placeholder="Enter Purpose of the Request" wire:model="purpose" required/>
                <div class="flex justify-end">
                    <flux:button 
                        class="!bg-red-800 !text-white" 
                        wire:click="requestACM({{ $template->id }})"
                        >
                        Request ACM
                    </flux:button>

                </div>
            </flux:modal>

        </div>
         @endforeach
    </div>
    <script>
        function previewPdf(templateId, fileUrl) {
    const container = document.getElementById(`pdf-scroll-view-${templateId}`);
    container.innerHTML = '<p style="text-align:center;color:#888;">Loading PDF...</p>';

    pdfjsLib.getDocument(fileUrl).promise.then(pdf => {
        container.innerHTML = '';
        for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
            renderPage(pdf, pageNum, container);
        }
    });
}

function renderPage(pdf, pageNum, container) {
    pdf.getPage(pageNum).then(page => {
        const viewport = page.getViewport({ scale: 1 });
        const scale = Math.min(window.innerWidth * 0.7 / viewport.width, 1.5);
        const scaledViewport = page.getViewport({ scale });

        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = scaledViewport.width;
        canvas.height = scaledViewport.height;
        canvas.style.display = 'block';
        canvas.style.margin = '0 auto 24px auto';

         canvas.oncontextmenu = (e) => e.preventDefault();
        canvas.ondragstart = (e) => e.preventDefault();
        canvas.style.userSelect = 'none'; // Disable text selection
        canvas.style.pointerEvents = 'auto'; // Keep interaction if needed

        page.render({ canvasContext: ctx, viewport: scaledViewport }).promise.then(() => {
            // Optional: add watermark
            const logo = new Image();
            logo.src = '/dhvsu.JPG';
            logo.onload = () => {
                ctx.save();
                ctx.globalAlpha = 0.11;
                const logoWidth = canvas.width * 0.7;
                const logoHeight = logo.height * (logoWidth / logo.width);
                ctx.drawImage(logo, (canvas.width - logoWidth) / 2, (canvas.height - logoHeight) / 2, logoWidth, logoHeight);
                ctx.restore();
            };
        });

        container.appendChild(canvas);
    });
}

    </script>
</div>
