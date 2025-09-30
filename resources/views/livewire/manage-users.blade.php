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
                        <th class="px-6 py-3 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">
                            Status
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $user->status }}
                            </td>
                            <td class="px-6 py-4 flex gap-2 justify-end whitespace-nowrap text-sm">
                            @if($user->status === 'pending')
                                <flux:button class="mr-2" icon="check" size="sm" class="!bg-green-500 !text-white" wire:click="approveUser({{ $user->id }})">Verify</flux:button>
                            @endif
                            <flux:modal.trigger :name="'edit-user'.$user->id" wire:click="editUser({{ $user->id }})">
                                <flux:button class="mr-2" icon="pencil-square" size="sm" class=""/>
                            </flux:modal.trigger> 
                             <flux:modal.trigger name="delete-user">
                                <flux:button variant="danger" icon="trash" size="sm" class=""/>
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

                    <!-- update user modal -->
                    <flux:modal :name="'edit-user'.$user->id" class="md:w-96">
                        @if ($selectedUser && $selectedUser->id === $user->id)
                            <div class="space-y-6">
                                <div>
                                    <flux:heading size="lg">Update user</flux:heading>
                                    <flux:text class="mt-2">Update user datails</flux:text>
                                </div>
                                <flux:input label="Name" placeholder="Name" wire:model.defer="updateName"/>
                                <flux:input label="Student Number" placeholder="Student number" wire:model.defer="updateStudentNumber"/>
                                <flux:input label="Email" placeholder="Email" wire:model.defer="updateEmail"/>
                                <div class="flex">
                                    <flux:spacer />
                                    <flux:button type="submit" variant="primary" >Update User</flux:button>
                                </div>
                            </div>
                        @else
                            <div class="flex justify-center p-6">
                                <flux:icon.loading size="lg" /> 
                            </div>
                        @endif
                    </flux:modal>
                    @endforeach
                </tbody>
            </table>
        </div>


        <!-- delete user moda -->
          
    </div>
</div>
