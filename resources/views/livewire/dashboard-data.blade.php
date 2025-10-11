<div wire:poll.visible.5s="updateCounts">
        <div class="grid gap-6 md:grid-cols-2">
            <!-- Users -->
            <div class="group flex flex-col items-center justify-center rounded-2xl border bg-zinc-50 py-6">
                <div class="flex items-center gap-3 text-red-800">
                    
                    <span class="text-xl font-semibold flex items-center"><flux:icon.users class="size-6 mr-2"/> Total Users</span>
                </div>
                <p class="mt-4 text-5xl font-extrabold text-red-900">{{ $userCount }}</p>
            </div>

            <!-- Research Projects -->
            <div class="group flex flex-col items-center justify-center rounded-2xl border bg-zinc-50 py-6">
                <div class="flex items-center gap-3 text-red-800">

                    <span  class="text-xl font-semibold flex items-center"><flux:icon.folder class="size-6 mr-2"/> Total Research Projects</span>
                </div>
                <p class="mt-4 text-5xl font-extrabold text-red-900">{{ $projectCount }}</p>
            </div>
        </div>
</div>
