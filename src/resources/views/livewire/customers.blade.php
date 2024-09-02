<div class="p-6 lg:p-8 bg-white border-b border-gray-200">
    @if (@session()->has('message')) 
        <div class="flex items-center border rounded-sm bg-blue-500 text-white text-sm font-bold px-4 py-3 relative" role="alert" x-data="{show: true}" x-show="show">
            <svg class="fill-current w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path
                    d="M12.432 0c1.34 0 2.01.912 2.01 1.957 0 1.305-1.164 2.512-2.679 2.512-1.269 0-2.009-.75-1.974-1.99C9.789 1.436 10.67 0 12.432 0zM8.309 20c-1.058 0-1.833-.652-1.093-3.524l1.214-5.092c.211-.814.246-1.141 0-1.141-.317 0-1.689.562-2.502 1.117l-.528-.88c2.572-2.186 5.531-3.467 6.801-3.467 1.057 0 1.233 1.273.705 3.23l-1.391 5.352c-.246.945-.141 1.271.106 1.271.317 0 1.357-.392 2.379-1.207l.6.814C12.098 19.02 9.365 20 8.309 20z" />
            </svg>
            <p>{{ session('message') }}</p>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3" @click="show = false">
                <svg class="fill-current h-5 w-5 text-white" role="button" xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20">
                    <title>Close</title>
                    <path
                        d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z" />
                </svg>
            </span>
        </div>
    @endif
    <div class="mt-8 text-2xl flex justify-between">
        <div>Customers</div>
        <div class="mr-2">
            <x-button wire:click="confirmCustomerAddition" class="bg-blue-800 hover:bg-blue-950">
                {{ __('Add new Customer') }}
            </x-button>
        </div>
    </div>
    <div class="mt-6">
        <div class="flex justify-between">
            <div class="pb-5">
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
                            <x-button wire:click="confirmCustomerEditing({{ $customer->id }})"
                                class="bg-blue-800 hover:bg-blue-950">
                                {{ __('Edit') }}
                            </x-button>
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

    <x-confirmation-modal wire:model.live="confirmingCustomerDeletion">
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
    </x-confirmation-modal>


    <x-dialog-modal wire:model.live="confirmingCustomerAddition">
        <x-slot name="title">
            {{ isset($this->customer['id']) ? 'Edit Customer' : 'Add Customer' }}
        </x-slot>


        <x-slot name="content">
            <div class="col-span-6 sm:col-span-4 mt-2">
                <x-label for="NIC" value="{{ __('NIC') }}" />
                <x-input id="NIC" name="customer.NIC" type="text" class="mt-1 block w-full"
                    wire:model="customer.NIC" required />
                <x-input-error for="customer.NIC" class="mt-2" />
            </div>
            <div class="col-span-6 sm:col-span-4 mt-2">
                <x-label for="name" value="{{ __('Name') }}" />
                <x-input id="name" name="customer.name" type="text" class="mt-1 block w-full"
                    wire:model="customer.name" required />
                <x-input-error for="customer.name" class="mt-2" />
            </div>
            <div class="col-span-6 sm:col-span-4 mt-2">
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" name="customer.email" type="text" class="mt-1 block w-full"
                    wire:model="customer.email" required />
                <x-input-error for="customer.email" class="mt-2" />
            </div>
            <div class="col-span-6 sm:col-span-4 mt-2">
                <x-label for="phone" value="{{ __('Phone') }}" />
                <x-input id="phone" name="customer.phone" type="text" class="mt-1 block w-full"
                    wire:model="customer.phone" required />
                <x-input-error for="customer.phone" class="mt-2" />
            </div>
            <div class="col-span-6 sm:col-span-4 mt-2">
                <x-label for="address" value="{{ __('Address') }}" />
                <x-input id="address" name="customer.address" type="text" class="mt-1 block w-full"
                    wire:model="customer.address" required />
                <x-input-error for="customer.address" class="mt-2" />
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
