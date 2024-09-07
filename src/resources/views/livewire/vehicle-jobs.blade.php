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
        @if ($cars->isNotEmpty())
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
                    @forelse($cars as $car)
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
                    @empty
                        <tr>
                            <td colspan="6" class="border px-4 py-4 text-center">
                                {{ __('-- No cars found --') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <x-dialog-modal wire:model.live="confirmingJobAddition">
                <x-slot name="title">
                    {{ __('Add Vehicle Job') }}
                </x-slot>

                <x-slot name="content">
                    <!-- Dropdown for Wash Types -->
                    <div class="col-span-6 sm:col-span-4 mt-2">
                        <x-label for="wash_type" value="{{ __('Wash Type') }}" />
                        <select id="wash_type" name="job.wash_type"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            wire:model="job.wash_type" required>
                            <option value="">{{ __('Select Wash Type') }}</option>
                            @foreach ($washing_services as $service)
                                <option value="{{ $service->name }}">{{ $service->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error for="job.wash_type" class="mt-2" />
                    </div>

                    <!-- Dropdown for Interior Cleaning Types -->
                    <div class="col-span-6 sm:col-span-4 mt-2">
                        <x-label for="interior_cleaning" value="{{ __('Interior Cleaning Type') }}" />
                        <select id="interior_cleaning" name="job.interior_cleaning"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            wire:model="job.interior_cleaning" required>
                            <option value="">{{ __('Select Interior Cleaning Type') }}</option>
                            @foreach ($interior_cleaning_services as $service)
                                <option value="{{ $service->name }}">{{ $service->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error for="job.interior_cleaning" class="mt-2" />
                    </div>

                    <!-- Checkboxes for Other Services -->
                    <div class="col-span-6 sm:col-span-4 mt-2">
                        <x-label value="{{ __('Select Services') }}" />
                        <div class="mt-2 space-y-2">
                            @foreach ($other_services as $service)
                                <label class="flex items-center">
                                    <input type="checkbox" value="{{ $service->id }}" wire:model="selected_services"
                                        class="form-checkbox">
                                    <span class="ml-2">{{ $service->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        <x-input-error for="selected_services" class="mt-2" />
                    </div>
                </x-slot>


                <x-slot name="footer">
                    <x-secondary-button wire:click="cancelJobModel" wire:loading.attr="disabled">
                        {{ __('Cancel') }}
                    </x-secondary-button>
                    <x-danger-button class="ms-3" wire:click="saveVehicleJob({{ $car->id }})"
                        wire:loading.attr="disabled">
                        {{ __('Save') }}
                    </x-danger-button>
                </x-slot>
            </x-dialog-modal>
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

        @if ($vehicleJobs->isNotEmpty())
            <table class="table-auto w-full mt-6">
                <thead>
                    <tr>
                        <th class="px-4 py-2">
                            <div class="flex items-center">Job Number</div>
                        </th>
                        <th class="px-4 py-2">
                            <div class="flex items-center">Car Registration Number/ Model</div>
                        </th>
                        <th class="px-4 py-2">
                            <div class="flex items-center">Status</div>
                        </th>
                        <th class="px-4 py-2">
                            <div class="flex items-center">Action</div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($vehicleJobs as $job)
                        <tr>
                            <td class="border px-4 py-2 ">{{ $job->id }}</td>
                            <td class="border px-4 py-2 ">
                                {{ $job->cars->registration_number }} /
                                {{ $job->cars->model }}
                            </td>
                            <td class="border px-4 py-2 ">
                                @if ($job->status === 'pending')
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium bg-orange-400 text-white">
                                        {{ __('Pending') }}
                                    </span>
                                @elseif($job->status === 'completed')
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium bg-green-400 text-white">
                                        {{ __('Completed') }}
                                    </span>
                                @endif
                            </td>
                            <td class="border px-4 py-2 ">
                                <x-button wire:click="confirmJobView({{ $job->id }})"
                                    class="!bg-yellow-500 hover:!bg-yellow-600">
                                    {{ __('View details') }}
                                </x-button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
            @if ($confirmingJobView)
                <x-dialog-modal wire:model="confirmingJobView">
                    <x-slot name="title">
                        {{ __('View Vehicle Job') }}
                    </x-slot>

                    <x-slot name="content">
                        <!-- Modal or section to display services -->
                        <div class="mt-4">
                            <table class="table-auto w-full mt-6">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 text-sm">Service Name</th>
                                        <th class="px-4 py-2 text-sm">Status</th>
                                        <th class="px-4 py-2 text-sm">Change Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($jobServices as $service)
                                        <tr>
                                            <td class="border px-4 py-2">{{ $service->name }}</td>
                                            <td class="border px-4 py-2">
                                                @if ($service->pivot->status === 'pending')
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium bg-orange-400 text-white">
                                                        {{ __('Pending') }}
                                                    </span>
                                                @elseif($service->pivot->status === 'completed')
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium bg-green-400 text-white">
                                                        {{ __('Completed') }}
                                                    </span>
                                                @endif
                                            </td>

                                            <td class="border px-4 py-2">
                                                <select wire:model="serviceStatuses.{{ $service->id }}"
                                                    class="rounded-lg text-sm">
                                                    <option value="pending">Pending</option>
                                                    <option value="completed">Completed</option>
                                                </select>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </x-slot>

                    <x-slot name="footer">
                        <x-secondary-button wire:click="cancelJobView" wire:loading.attr="disabled">
                            {{ __('Cancel') }}
                        </x-secondary-button>
                        <x-danger-button class="ms-3" wire:click="updateJobServiceStatuses"
                            wire:loading.attr="disabled">
                            {{ __('Update') }}
                        </x-danger-button>
                    </x-slot>
                </x-dialog-modal>
            @endif
        @else
            <div class="px-4 py-4 text-center">
                {{ __('-- No Vehicle jobs added--') }}
            </div>
        @endif

    </div>
