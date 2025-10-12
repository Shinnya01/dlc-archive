<div class="h-auto w-full rounded-xl border border-neutral-200 bg-zinc-50 p-4 grid gap-6" wire:poll.visible.5s="loadRecentLogins">
    <h1 class="text-3xl font-bold text-red-900">User Logins</h1>
    <div class="max-h-74 overflow-y-auto">
        <table class="min-w-full divide-y divide-gray-300 bg-white rounded-lg">
            <thead class="bg-gray-100 text-gray-700 sticky top-0">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-semibold">#</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Name</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Login at</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 text-sm text-gray-800">
                @forelse($recentLogins as $login)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3">{{ $login->user->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3">{{ $login->logged_in_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-6 text-center text-gray-500">
                            No recent logins found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
