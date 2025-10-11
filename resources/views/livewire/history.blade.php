<div class="relative flex flex-col h-full w-full overflow-hidden rounded-xl border border-neutral-200 bg-zinc-50 p-4" wire:poll.visible.5s="refreshHistory">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-3xl font-bold text-red-900">History</h1>

        <div class="flex gap-2">
            <!-- Date Filter -->
            <input
                type="date"
                wire:model.live="date"
                class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:border-red-500 focus:ring focus:ring-red-200"
            />

            <!-- Search -->
            <flux:input
                wire:model.live="search"
                icon:trailing="magnifying-glass"
                placeholder="Search History"
            />
        </div>
    </div>

    <!-- Table -->
    <div class="h-full max-h-88 lg:max-h-144 overflow-y-auto rounded-lg bg-zinc-200">
        <table class="min-w-full divide-y divide-gray-300 bg-white rounded-lg">
            <thead class="bg-gray-100 text-gray-700 sticky top-0">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-semibold">#</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Name</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Detail</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Date</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 text-sm text-gray-800">
                @forelse ($histories as $history)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3">{{ $history->user->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3">{{ $history->detail }}</td>
                        <td class="px-4 py-3">{{ $history->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-gray-500">
                            No history records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

