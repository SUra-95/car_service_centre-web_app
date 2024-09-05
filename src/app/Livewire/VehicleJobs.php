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
    public $wash_type;
    public $interior_type;
    public $selectedServices = [];
    public $services;
    public $car = [
        'id' => '',
        'customer_id' => null,
        'registration_number' => '',
        'model' => '',
        'fuel_type' => '',
        'transmission' => '',
    ];
    protected $queryString = [
        'search' => ['except' => '']
    ];
    protected $rules = [
        'car.customer_id' => 'required|exists:customers,id',
        'car.registration_number' => 'required|string|max:20',
        'car.model' => 'required|string|max:255',
        'car.fuel_type' => 'required|string|max:255',
        'car.transmission' => 'required|string|max:15',
    ];

    public function mount()
    {
        $this->customers = Customer::all();
        $this->services = Service::all();
    }

    public function confirmJobAddition()
    {
        $this->confirmingJobAddition = true;
    }

    public function cancelJobModel(){
        $this->confirmingJobAddition = false;
    }
    
    public function saveVehicleJob(Car $car)
    {
        dd($car);
        $this->validate([
            'wash_type' => 'required',
            'interior_type' => 'required',
            'selectedServices' => 'required|array|min:1',
        ]);

        // Create the new VehicleJob entry
        $vehicleJob = VehicleJob::create([
            'car_id' => $car->id,
            'status' => 'pending',
        ]);

        // Attach services to the job
        $vehicleJob->services()->attach($this->selectedServices);

        session()->flash('message', 'Vehicle job added successfully!');
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
