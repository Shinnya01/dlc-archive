<x-layouts.app.sidebar :title="$title ?? null">
    <flux:main class="!bg-stone-200">
        {{ $slot }}
    </flux:main>
    <x-toaster-hub />
    
</x-layouts.app.sidebar>
