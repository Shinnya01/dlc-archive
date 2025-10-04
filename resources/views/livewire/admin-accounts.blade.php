<div>
    {{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}
    <div class="flex items-center justify-between">
        <h1 class="text-4xl font-bold text-red-900">Manage Admin</h1>
        <div class="flex justify-end w-lg">
            <flux:modal.trigger name="add-admin">
            <flux:button icon="plus" class="cursor-pointer">Create Admin</flux:button>
            </flux:modal.trigger>
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
                            Email
                        </th>
                        <th class="px-6 py-3 text-right text-sm font-bold text-gray-700 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($admins as $admin)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $admin->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $admin->email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm ">
                                <flux:modal.trigger :name="'update-admin'.$admin->id" wire:click="editAdmin({{ $admin->id }})">
                                    <flux:button class="mr-2" icon="pencil-square" size="sm" />
                                </flux:modal.trigger>

                                <flux:modal.trigger :name="'delete-admin'.$admin->id">
                                <flux:button variant="danger" icon="trash" size="sm" class="size-4"/>
                                </flux:modal.trigger>
                            </td>
                        </tr>

                          <!-- Delete modal -->
                         <flux:modal :name="'delete-admin' . $admin->id" class="md:w-96">
                                    <div class="space-y-6">
                                        <!-- Header -->
                                        <div class="text-start">
                                            <flux:heading size="lg" class="text-red-600">Delete Admin</flux:heading>
                                            <flux:text class="mt-2 text-gray-600">
                                                 Are you sure you want to delete 
                                                 <span class="font-semibold text-gray-900">{{ $admin->name }}</span>?
                                            </flux:text>
                                            <flux:text class="mt-1 text-sm text-gray-500">
                                                  This action cannot be undone.
                                              </flux:text>
                                            </div>
                                        {{-- Action Buttons --}}
                                        
                                        <div class="flex justify-end space-x-3">
                                            <flux:modal.close>
                                            <flux:button variant="ghost" >
                                                Cancel
                                            </flux:button>
                                            </flux:modal.close>
                                              <flux:button 
                                                 type="delete" 
                                                   variant="danger" 
                                                   class="px-4 py-2" 
                                                wire:click="removeAdmin({{ $admin->id }})"
                                              >
                                                 Delete
                                             </flux:button>
                                         </div>
                                     </div>
                                </flux:modal>

                         <flux:modal :name="'update-admin'.$admin->id" class="min-w-sm">
                            <div class="space-y-6 transition">
                                @if ($selectedAdmin && $selectedAdmin->id === $admin->id)
                                    <div>
                                        <flux:heading size="lg">Edit admin</flux:heading>
                                        <flux:text class="mt-2">Edit admin acc</flux:text>
                                    </div>
                                    <flux:input label="Name" wire:model.defer="updateName" placeholder="e.g. Juan Carlo" />
                                    <flux:input label="Email" wire:model.defer="updateEmail" placeholder="e.g. juan@gmail.com" />
                                    
                                    <div class="flex">
                                        <flux:spacer />
                                        <flux:button 
                                            type="submit" 
                                            variant="primary" 
                                            wire:click="updateAdmin({{ $admin->id }})" 
                                            wire:loading.attr="disabled">
                                                <span wire:loading.remove>Update</span>
                                                <span wire:loading>Updating...</span>
                                        </flux:button>
                                    </div>
                                @else
                                    <div class="flex justify-center p-6">
                                        <flux:icon.loading size="lg" /> <!-- Shows loading spinner -->
                                    </div>
                                @endif
                            </div>
                        </flux:modal>

                    @endforeach
                    @fluxScripts
                </tbody>
            </table>
        </div>
        <!-- add admin modal -->
        <flux:modal name="add-admin" class="md:w-96">
            <form wire:submit.prevent="createAdmin">
                <div class="space-y-6">
                    <div>
                        <flux:heading size="lg">Create new admin</flux:heading>
                        <flux:text class="mt-2">Make another admin acc</flux:text>
                    </div>
                    <flux:input label="Name" wire:model.defer="name" placeholder="e.g. Juan Carlo" />
                    <flux:input label="Email" wire:model.defer="email" placeholder="e.g. juan@gmail.com" />
                    <flux:input label="Password" wire:model="password" placeholder="Password" />
                    <div class="flex">
                        <flux:spacer />
                        <flux:button type="submit" variant="primary">Create</flux:button>
                    </div>
                </div>
            </form>
       </flux:modal>

    </div>
</div>
