<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
        <h1 class="text-5xl font-bold text-red-900">Welcome, {{ $name }}</h1>

        <!-- Stats Cards -->
        <div class="grid gap-6 md:grid-cols-2">
            <!-- Users -->
            <div class="group flex flex-col items-center justify-center rounded-2xl border bg-gradient-to-br from-amber-50 to-amber-100 shadow-lg p-8 transition hover:shadow-2xl hover:-translate-y-1">
                <div class="flex items-center gap-3 text-red-800">
                    <!-- <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M17 20h5v-2a4 4 0 00-4-4h-1m-4 6v-2a4 4 0 00-4-4H7a4 4 0 00-4 4v2h5m6-10a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg> -->
                    <span icon="user-circle" class="text-xl font-semibold">Total Users</span>
                </div>
                <p class="mt-4 text-5xl font-extrabold text-red-900">{{ $userCount }}</p>
            </div>

            <!-- Research Projects -->
            <div class="group flex flex-col items-center justify-center rounded-2xl border bg-gradient-to-br from-amber-50 to-amber-100 shadow-lg p-8 transition hover:shadow-2xl hover:-translate-y-1">
                <div class="flex items-center gap-3 text-red-800">
                    <!-- <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 20h9M12 4h9m-9 8h9M3 4h.01M3 12h.01M3 20h.01"/>
                    </svg> -->
                    <span  class="text-xl font-semibold">Total Research Projects</span>
                </div>
                <p class="mt-4 text-5xl font-extrabold text-red-900">{{ $projectCount }}</p>
            </div>
        </div>

        <!-- Background Pattern Placeholder -->
        <!-- <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200">
            <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20" />
        </div> -->
    </div>
</x-layouts.app>
