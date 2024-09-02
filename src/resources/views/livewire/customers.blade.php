<div class="p-6 lg:p-8 bg-white border-b border-gray-200">
    <div class="mt-8 text-2xl flex justify-between">
        <div>Items</div>
        <div class="mr-2">
            <x-button wire:click="confirmCustomerAddition" class="bg-blue-800 hover:bg-blue-950">
                {{ __('Add new Customer') }}
            </x-button>
        </div>
    </div>
    <div class="mt-6">
        <div class="flex justify-between">
            <div class="p-2">
                <input wire:model.live.debounce.500ms="search" type="text" name="" placeholder="search"
                    class="shadow appearance-none border  rounded w-full py-2 px-3 text-gray leading-tight focus:outline-none focus:shadow-outline ">
            </div>
        </div>
        <table class="table-auto w-full">
            <thead>
                <tr>
                    <th class="px-4 py-2">
                        <div class="flex items-center">NIC</div>
                    </th>
                    <th class="px-4 py-2">
                        <div class="flex items-center">Name</div>
                    </th>
                    <th class="px-4 py-2">
                        <div class="flex items-center">Email</div>
                    </th>
                    <th class="px-4 py-2">
                        <div class="flex items-center">Phone</div>
                    </th>
                    <th class="px-4 py-2">
                        <div class="flex items-center">Action</div>
                    </th>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($customers as $customer)
                    <tr>
                        <td class="border px-4 py-2 ">{{ $customer->NIC }}</td>
                        <td class="border px-4 py-2 ">{{ $customer->name }}</td>
                        <td class="border px-4 py-2 ">{{ $customer->email }}</td>
                        <td class="border px-4 py-2 ">{{ $customer->phone }}</td>
                        <td class="border px-4 py-2 ">
                            Edit
                            <x-danger-button wire:click="confirmCustomerDeletion({{ $customer->id }})"
                                wire:loading.attr="disabled">
                                {{ __('Delete') }}
                            </x-danger-button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $customers->links() }}
    </div>

    <x-dialog-modal wire:model.live="confirmingCustomerDeletion">
        <x-slot name="title">
            {{ __('Delete Customer') }}
        </x-slot>

        <x-slot name="content">
            {{ __('Are you sure you want to delete this customer?') }}
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="cancelDeleteModel" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-danger-button class="ms-3" wire:click="deleteCustomer({{ $confirmingCustomerDeletion }})"
                wire:loading.attr="disabled">
                {{ __('Delete') }}
            </x-danger-button>
        </x-slot>
    </x-dialog-modal>


    <x-dialog-modal wire:model.live="confirmingCustomerAddition">
        <x-slot name="title">
            {{ __('Add Customer') }}
        </x-slot>

        <x-slot name="content">
            <div class="col-span-6 sm:col-span-4 mt-2">
                <x-label for="NIC" value="{{ __('NIC') }}" />
                <x-input id="NIC" name="NIC" type="text" class="mt-1 block w-full" wire:model="NIC" required />
                <x-input-error for="NIC" class="mt-2" />
            </div>
            <div class="col-span-6 sm:col-span-4 mt-2">
                <x-label for="name" value="{{ __('Name') }}" />
                <x-input id="name" name="name" type="text" class="mt-1 block w-full" wire:model="name" required />
                <x-input-error for="name" class="mt-2" />
            </div>
            <div class="col-span-6 sm:col-span-4 mt-2">
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" name="email" type="text" class="mt-1 block w-full" wire:model="email" required />
                <x-input-error for="email" class="mt-2" />
            </div>
            <div class="col-span-6 sm:col-span-4 mt-2">
                <x-label for="phone" value="{{ __('Phone') }}" />
                <x-input id="phone" name="phone" type="text" class="mt-1 block w-full" wire:model="phone" required />
                <x-input-error for="phone" class="mt-2" />
            </div>
            <div class="col-span-6 sm:col-span-4 mt-2">
                <x-label for="address" value="{{ __('Address') }}" />
                <x-input id="address" name="address" type="text" class="mt-1 block w-full" wire:model="address" required />
                <x-input-error for="address" class="mt-2" />
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="cancelAddModel" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-danger-button class="ms-3" wire:click="saveCustomer" wire:loading.attr="disabled">
                {{ __('Save') }}
            </x-danger-button>
        </x-slot>
    </x-dialog-modal>
</div>
