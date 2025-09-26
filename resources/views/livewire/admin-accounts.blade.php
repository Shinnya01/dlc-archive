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
                                <flux:modal.trigger name="edit-admin">
                                <flux:button class="mr-2" icon="pencil-square" size="sm" class="size-4"/>
                                </flux:modal.trigger>
                                <flux:modal.trigger name="delete-admin">
                                <flux:button variant="danger" icon="trash" size="sm" class="size-4"/>
                                </flux:modal.trigger>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- add admin modal -->
        <flux:modal name="add-admin" class="md:w-96">
           <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Create new admin</flux:heading>
                    <flux:text class="mt-2">Make another admin acc</flux:text>
                </div>
                <flux:input label="Name" placeholder="e.g. Juan Carlo" />
                <flux:input label="Email" placeholder="e.g. juan@gmail.com" />
                <flux:input label="Password" placeholder="Password" />
                <div class="flex">
                    <flux:spacer />
                    <flux:button type="submit" variant="primary">Create</flux:button>
                </div>
          </div>
       </flux:modal>

       <!-- edit admin modal -->
        <flux:modal name="edit-admin" class="md:w-96">
           <div class="space-y-6">
                <div>
                    <flux:heading size="lg"> Edit admin</flux:heading>
                    <flux:text class="mt-2">Edit admin acc</flux:text>
                </div>
                <flux:input label="Name" placeholder="e.g. Juan Carlo" />
                <flux:input label="Email" placeholder="e.g. juan@gmail.com" />
                <flux:input label="Password" placeholder="Password" />
                <div class="flex">
                    <flux:spacer />
                    <flux:button type="submit" variant="primary">Create</flux:button>
                </div>
          </div>
       </flux:modal>

       <!-- admin acc delete modal -->
        <flux:modal name="delete-admin" class="md:w-96">
           <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Are you sure to delete this admin?</flux:heading>
                    <flux:text class="mt-2">This action can't be undone</flux:text>
                </div>

                <div class="flex">
                    <flux:spacer />
                    <flux:button type="cancel" variant="primary">Cancel</flux:button>
                    <flux:button type="delete" variant="danger">Delete</flux:button>
                </div>
          </div>
       </flux:modal>

    </div>
</div>
