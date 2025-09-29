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

                            <flux:modal.trigger name="edit-user">
                                <flux:button class="mr-2" icon="pencil-square" size="sm" class="size-4"/>
                            </flux:modal.trigger>    
                             <flux:modal.trigger name="delete-user">
                                <flux:button variant="danger" icon="trash" size="sm" class="size-4"/>
                            </flux:modal.trigger>   
                            </td>
                        </tr>
                     <flux:modal name="delete-user" class="md:w-96">
                        <div class="space-y-6">
                            <div>
                                <flux:heading :name="'delete-user'.$user->id" size="lg">Are you sure to delete this user?</flux:heading>
                                <flux:text class="mt-2">This action can't be undone</flux:text>
                            </div>
                            <div class="flex">
                                <flux:spacer />
                                <flux:button type="submit" variant="primary">Cancel</flux:button>
                                <flux:button type="submit" variant="danger" wire:click="removeUser({{ $user->id }})">Delete</flux:button>
                            </div>
                        </div>
                    </flux:modal>
                    @endforeach
                </tbody>
            </table>
        </div>
      <!-- update user modal -->
                <flux:modal name="edit-user" class="md:w-96">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Update user</flux:heading>
                    <flux:text class="mt-2">Update user datails</flux:text>
                </div>
                <flux:input label="Name" placeholder="Name" />
                <flux:input label="Student Number" placeholder="Student number" />
                <flux:input label="Email" placeholder="Email" />
                <div class="flex">
                    <flux:spacer />
                    <flux:button type="submit" variant="primary">Save changes</flux:button>
                </div>
            </div>
        </flux:modal>

        <!-- delete user moda -->
          
    </div>
</div>
