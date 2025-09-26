<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <h1 class="text-5xl">Welcome, Tester Account</h1>
        <div class="grid auto-rows-min gap-4 md:grid-cols-2">
            <div class="min-h-96 text-2xl font-medium flex flex-col items-center justify-center rounded-xl border bg-stone-100 text-center shadow-xl">
                Total Users
                <p class="text-5xl">12</p>
            </div>
            <div class="min-h-96 text-2xl font-medium flex flex-col items-center justify-center rounded-xl border bg-stone-100 text-center shadow-xl">
                Total Research Projects
                <p class="text-5xl">12</p>

            </div>
            
        </div>
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200">
            <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20" />
        </div>
    </div>
</x-layouts.app>
