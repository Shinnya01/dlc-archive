<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
        <h1 class="text-5xl font-bold text-red-900">Welcome, {{ $name }}</h1>

        <!-- Stats Cards -->
        <livewire:dashboard-data/>

        <!-- Background Pattern Placeholder -->
  
        <livewire:history/>
        

    </div>
</x-layouts.app>
