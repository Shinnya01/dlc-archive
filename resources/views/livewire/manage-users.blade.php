<div>
    {{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}
    <div class="flex items-center justify-between">
        <h1 class="text-4xl font-bold text-red-900">Manage Users</h1>
        <div class="flex justify-end w-lg">
            <flux:input icon="magnifying-glass" placeholder="Search Users" />
        </div>
    </div>
    <div class="mt-6 p-4 bg-white rounded-xl">
        <div class="overflow-x-auto bg-white shadow-md rounded-xl">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-amber-100">
                    <tr class="">
                        <th class="px-6 py-3 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">
                            Name
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">
                            Student Number
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">
                            Email
                        </th>
                        <th class="px-6 py-3 text-right text-sm font-bold text-gray-700 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($users as $user)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $user->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $user->student_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm ">
                                <flux:button class="mr-2" icon="pencil-square" size="sm" class="size-4"/>
                                <flux:button variant="danger" icon="trash" size="sm" class="size-4"/>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
