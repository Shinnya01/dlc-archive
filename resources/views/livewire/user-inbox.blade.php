<div>
    {{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}
    <div class="flex items-center justify-between">
        <h1 class="text-4xl font-bold text-red-900">My ACM Requests</h1>
        <div class="flex justify-end w-lg">
        </div>
    </div>
    <div class="mt-6 p-4 bg-white rounded-xl">
        <div class="overflow-x-auto bg-white shadow-md rounded-xl">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-amber-100">
                    <tr class="">
                        <th class="px-6 py-3 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">
                            Title
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">
                            Request Purpose
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">
                            Request Purpose
                        </th>
                        <th class="px-6 py-3 text-right text-sm font-bold text-gray-700 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($requests as $request)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $request->researchProject->title }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $request->purpose }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $request->status }}
                            </td>
     
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm ">
                                @if($request->status === 'pending')
                                    <flux:button variant="danger" icon="x-mark" size="sm" >Cancel</flux:button>
                                @else
                                    <flux:button class="mr-2" icon="folder-arrow-down" size="sm" href="{{ asset(str_replace('public/', 'storage/', $request->pdf_path)) }}">Download</flux:button>
                                    <flux:button variant="danger" icon="x-mark" size="sm" >Delete</flux:button>
                                @endif
                            </td>
                        </tr>                 
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>


</div>

