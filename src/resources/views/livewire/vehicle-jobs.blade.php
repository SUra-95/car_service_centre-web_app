<div class="p-6 lg:p-8 bg-white border-b border-gray-200">
    @if (@session()->has('message'))
        <div class="flex items-center border rounded-sm bg-blue-500 text-white text-sm font-bold px-4 py-3 relative"
            role="alert" x-data="{ show: true }" x-show="show">
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
        <div>Vehicle Jobs</div>
        <div class="mr-2">
            {{-- <x-button wire:click="confirmJobAddition" class="!bg-blue-800 hover:!bg-blue-900">
                {{ __('Add new job') }}
            </x-button> --}}
        </div>
    </div>
    <div class="mt-6">
        <div class="flex justify-start pb-6">
            <div class="w-full">
                <input wire:model.live.debounce.500ms="search" type="text" name=""
                    placeholder="Search by NIC or email"
                    class="shadow appearance-none border  rounded w-full py-2 px-3 h-10 text-gray leading-tight focus:outline-none focus:shadow-outline ">
            </div>
        </div>
        @if($cars->isNotEmpty())
            <table class="table-auto w-full">
                <thead>
                    <tr>
                        <th class="px-4 py-2">
                            <div class="flex items-center">Registration Number</div>
                        </th>
                        <th class="px-4 py-2">
                            <div class="flex items-center">Model</div>
                        </th>
                        <th class="px-4 py-2">
                            <div class="flex items-center">Fuel Type</div>
                        </th>
                        <th class="px-4 py-2">
                            <div class="flex items-center">Transmission</div>
                        </th>
                        <th class="px-4 py-2">
                            <div class="flex items-center">Customer</div>
                        </th>
                        <th class="px-4 py-2">
                            <div class="flex items-center">Action</div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cars as $car)
                        <tr>
                            <td class="border px-4 py-2 ">{{ $car->registration_number }}</td>
                            <td class="border px-4 py-2 ">{{ $car->model }}</td>
                            <td class="border px-4 py-2 ">{{ $car->fuel_type }}</td>
                            <td class="border px-4 py-2 ">{{ $car->transmission }}</td>
                            <td class="border px-4 py-2 ">{{ $car->customer->name }}</td>
                            <td class="border px-4 py-2 ">
                                <x-button wire:click="confirmJobAddition({{ $car->id }})"
                                    class="!bg-blue-800 hover:!bg-blue-900">
                                    {{ __('Initiate a job') }}
                                </x-button>
                                {{-- <x-danger-button wire:click="confirmCarDeletion({{ $car->id }})"
                                wire:loading.attr="disabled">
                                {{ __('Delete') }}
                            </x-danger-button> --}}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="px-4 py-4 text-center">
                {{ __('-- Please search a car by NIC or email of the customer and initiate a service --') }}
            </div>
        @endif
        {{-- <div class="mt-4">
        {{ $cars->links() }}
    </div> --}}

        {{-- <x-confirmation-modal wire:model.live="confirmingCarDeletion">
        <x-slot name="title">
            {{ __('Delete Car') }}
        </x-slot>
        <x-slot name="content">
            {{ __('Are you sure you want to delete this car?') }}
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="cancelDeleteModel" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>
            <x-danger-button class="ms-3" wire:click="deleteCar({{ $confirmingCarDeletion }})"
                wire:loading.attr="disabled">
                {{ __('Delete') }}
            </x-danger-button>
        </x-slot>
    </x-confirmation-modal> --}}

        <x-dialog-modal wire:model.live="confirmingJobAddition">
            <x-slot name="title">
                {{ __('Add Vehicle Job') }}
            </x-slot>

            <x-slot name="content">
                <!-- Dropdown for Wash Types -->
                <div class="col-span-6 sm:col-span-4 mt-2">
                    <x-label for="wash_type" value="{{ __('Wash Type') }}" />
                    <select id="wash_type" name="car.wash_type"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        wire:model="car.wash_type" required>
                        <option value="">{{ __('Select Wash Type') }}</option>
                        <option value="Full Wash">Full Wash</option>
                        <option value="Body Wash">Body Wash</option>
                    </select>
                    <x-input-error for="car.wash_type" class="mt-2" />
                </div>

                <!-- Dropdown for Interior Cleaning Types -->
                <div class="col-span-6 sm:col-span-4 mt-2">
                    <x-label for="interior_cleaning" value="{{ __('Interior Cleaning Type') }}" />
                    <select id="interior_cleaning" name="car.interior_cleaning"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        wire:model="car.interior_cleaning" required>
                        <option value="">{{ __('Select Interior Cleaning Type') }}</option>
                        <option value="Shampoo">Shampoo</option>
                        <option value="Vacuum">Vacuum</option>
                    </select>
                    <x-input-error for="car.interior_cleaning" class="mt-2" />
                </div>

                <!-- Checkboxes for Car Services -->
                <div class="col-span-6 sm:col-span-4 mt-2">
                    <x-label value="{{ __('Select Services') }}" />
                    <div class="mt-2 space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" value="Engine Oil Replacement" wire:model="selected_services"
                                class="form-checkbox">
                            <span class="ml-2">{{ __('Engine Oil Replacement') }}</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" value="Brake Oil Replacement" wire:model="selected_services"
                                class="form-checkbox">
                            <span class="ml-2">{{ __('Brake Oil Replacement') }}</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" value="Coolant Replacement" wire:model="selected_services"
                                class="form-checkbox">
                            <span class="ml-2">{{ __('Coolant Replacement') }}</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" value="Air Filter Replacement" wire:model="selected_services"
                                class="form-checkbox">
                            <span class="ml-2">{{ __('Air Filter Replacement') }}</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" value="Oil Filter Replacement" wire:model="selected_services"
                                class="form-checkbox">
                            <span class="ml-2">{{ __('Oil Filter Replacement') }}</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" value="AC Filter Replacement" wire:model="selected_services"
                                class="form-checkbox">
                            <span class="ml-2">{{ __('AC Filter Replacement') }}</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" value="Brake Shoe Replacement" wire:model="selected_services"
                                class="form-checkbox">
                            <span class="ml-2">{{ __('Brake Shoe Replacement') }}</span>
                        </label>
                    </div>
                    <x-input-error for="selected_services" class="mt-2" />
                </div>
            </x-slot>

            <x-slot name="footer">
                <x-secondary-button wire:click="cancelJobModel" wire:loading.attr="disabled">
                    {{ __('Cancel') }}
                </x-secondary-button>
                <x-danger-button class="ms-3" wire:click="saveVehicleJob({{ $car->id }})" wire:loading.attr="disabled">
                    {{ __('Save') }}
                </x-danger-button>
            </x-slot>
        </x-dialog-modal>

    </div>
