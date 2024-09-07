<?php

namespace App\Livewire;

use App\Models\Car;
use App\Models\Service;
use Livewire\Component;
use App\Models\Customer;
use App\Models\VehicleJob;

class VehicleJobs extends Component
{
    public $search;
    public $confirmingCarDeletion = false;
    public $confirmingJobAddition = false;
    public $password;
    public $customers;
    // public $cars;
    public $filter;
    public $washing_services = [];
    public $interior_cleaning_services = [];
    public $selected_services = [];
    public $other_services = [];
    public $services;
    public $job = [
        'wash_type' => '',
        'interior_cleaning' => '',
    ];
    protected $queryString = [
        'search' => ['except' => '']
    ];
    protected $rules = [
        'job.wash_type' => 'required|string',
        'job.interior_cleaning' => 'required|string',
        'selected_services' => 'required|array|min:1',
    ];

    public function mount()
    {
        $this->customers = Customer::all();
        $this->services = Service::all();
        $this->washing_services = Service::where('section', 'Washing')->get();
        $this->interior_cleaning_services = Service::where('section', 'Interior Cleaning')->get();
        $this->other_services = Service::where('section', 'Service')->get();
    }

    public function confirmJobAddition()
    {
        $this->confirmingJobAddition = true;
    }

    public function cancelJobModel()
    {
        $this->confirmingJobAddition = false;
    }

    public function saveVehicleJob($carId)
    {
        // Validate inputs
        $this->validate([
            'job.wash_type' => 'required',
            'job.interior_cleaning' => 'required',
            'selected_services' => 'required|array|min:1',
        ]);

        // Create the new VehicleJob record without the services
        $vehicleJob = VehicleJob::create([
            'status' => 'pending',
            'car_id' => $carId,
            'is_deleted' => 0,
        ]);

        // Save selected services from checkboxes
        if ($this->selected_services) {
            $vehicleJob->services()->attach($this->selected_services);
        }

        // Save washing and interior cleaning selections
        if ($this->job['wash_type']) {
            $washService = Service::where('name', $this->job['wash_type'])->first();
            $vehicleJob->services()->attach($washService->id);
        }

        if ($this->job['interior_cleaning']) {
            $interiorService = Service::where('name', $this->job['interior_cleaning'])->first();
            $vehicleJob->services()->attach($interiorService->id);
        }

        // Reset the form fields
        $this->reset(['job', 'selected_services', 'confirmingJobAddition']);

        // Flash success message or emit event
        session()->flash('message', 'Vehicle job created successfully!');

        // Close the modal
        $this->confirmingJobAddition = false;
    }



    public function render()
    {
        // Check if there is a search input
        if (!$this->search) {
            // If no search input, return an empty result set
            return view('livewire.vehicle-jobs', [
                'cars' => collect([]) // Pass an empty collection
            ]);
        }

        // Start with a customer query
        $customerQuery = Customer::query();

        // Apply search filters on the customer query
        if ($this->search) {
            $customerQuery->where(function ($q) {
                $q->where('email', 'like', "%{$this->search}%")
                    ->orWhere('NIC', 'like', "%{$this->search}%");
            });
        }

        // Retrieve the first matching customer
        $customer = $customerQuery->first();

        // Prepare a car query
        $carQuery = Car::query();

        // If a customer was found, filter cars by the customer_id
        if ($customer) {
            $carQuery->where('customer_id', $customer->id);
        } else {
            // If no customer found, return an empty result set
            return view('livewire.vehicle-jobs', [
                'cars' => collect([]) // Pass an empty collection
            ]);
        }

        // Paginate the car results
        $cars = $carQuery->paginate(4);

        // Return the view with the cars data
        return view('livewire.vehicle-jobs', [
            'cars' => $cars
        ]);
    }
}
