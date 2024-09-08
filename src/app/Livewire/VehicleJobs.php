<?php

namespace App\Livewire;

use App\Models\Car;
use App\Models\Service;
use Livewire\Component;
use App\Models\Customer;
use App\Models\VehicleJob;
use App\Mail\JobCompletedMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class VehicleJobs extends Component
{
    public $search;
    public $confirmingCarDeletion = false;
    public $confirmingJobAddition = false;
    public $confirmingJobView = false;
    public $password;
    public $customers;
    // public $cars;
    public $filter;
    public $washing_services = [];
    public $interior_cleaning_services = [];
    public $selected_services = [];
    public $other_services = [];
    public $services;
    public $jobServices;
    public $vehicleJobs;
    public $vehicleJob;
    public $servicesWithStatus = [];
    public $serviceStatuses = [];
    public $vehicleJobsCount;
    public $completedVehicleJobs = [];
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
        $this->vehicleJobs = VehicleJob::with('cars')->get();
        $this->vehicleJobsCount = VehicleJob::withCount('services')->get();
        $this->getVehicleJobs();
    }

    public function confirmJobAddition()
    {
        $this->confirmingJobAddition = true;
    }

    public function confirmJobView(VehicleJob $vehicleJob)
    {

        // Retrieve the services associated with this VehicleJob using the pivot table
        $this->jobServices = $vehicleJob->services()->withPivot('status')->get(); // Fetch related services

        $this->vehicleJob = $vehicleJob;
        foreach ($this->jobServices as $service) {
            $this->serviceStatuses[$service->id] = $service->pivot->status;
        }

        $this->confirmingJobView = true;
    }

    public function cancelJobModel()
    {
        $this->confirmingJobAddition = false;
    }

    public function cancelJobView()
    {
        $this->confirmingJobView = false;
    }

    public function saveVehicleJob($carId)
    {
        // Validate inputs
        $this->validate([
            'job.wash_type' => 'required',
            'job.interior_cleaning' => 'required',
            'selected_services' => 'required|array|min:1',
        ]);

        DB::beginTransaction(); // Start the transaction

        try {
            // Create the new VehicleJob record
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
                if ($washService) {
                    $vehicleJob->services()->attach($washService->id);
                }
            }

            if ($this->job['interior_cleaning']) {
                $interiorService = Service::where('name', $this->job['interior_cleaning'])->first();
                if ($interiorService) {
                    $vehicleJob->services()->attach($interiorService->id);
                }
            }

            DB::commit(); // Commit the transaction

            // Reset the form fields
            $this->reset(['job', 'selected_services', 'confirmingJobAddition']);

            // Flash success message or emit event
            session()->flash('message', 'Vehicle job created successfully!');

            // Close the modal
            $this->confirmingJobAddition = false;
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction on error

            // Handle the exception
            session()->flash('error', 'An error occurred while creating the vehicle job.');
        }
    }
    public function updateJobServiceStatuses()
    {
        // Check if vehicleJob is set
        if (!$this->vehicleJob) {
            session()->flash('error', 'Vehicle Job not found.');
            return;
        }

        $hasPendingServices = false; // Track if there are any pending services

        // Loop through each service's status and update the pivot table
        foreach ($this->serviceStatuses as $serviceId => $status) {
            // Check if at least one service is 'pending'
            if ($status === 'pending') {
                $hasPendingServices = true;
            }

            // Update the pivot table with the new status
            $this->vehicleJob->services()->updateExistingPivot($serviceId, ['status' => $status]);
        }

        // If no pending services exist, update the main table
        if (!$hasPendingServices) {
            // Update the vehicle_jobs table to mark it as 'completed' or another appropriate status
            $this->vehicleJob->update(['status' => 'completed']);

            $carId = $this->vehicleJob->car_id;
            $car = Car::find($carId);
            $customer = Customer::find($car->customer_id);

            if ($customer && $customer->email) {
                Mail::to($customer->email)->send(new JobCompletedMail($this->vehicleJob, $customer));
            }

            session()->flash('message', 'Service completed email sent successfully.');
        } else {
            // Update the vehicle_jobs table to mark it as 'pending' or retain current status
            $this->vehicleJob->update(['status' => 'pending']);
        }

        session()->flash('message', 'Service statuses updated successfully.');

        $this->cancelJobView();
    }

    public function loadVehicleJobs()
    {
        $this->vehicleJobsCount = VehicleJob::withCount('services')->get();
        dd($this->vehicleJobsCount);
    }

    public function getVehicleJobs()
    {
        $this->completedVehicleJobs = VehicleJob::with('services')->get()->map(function ($job) {
            // Total services for the job
            $totalServices = $job->services->count();
    
            // Completed services for the job
            $completedServices = $job->services->where('pivot.status', 'completed')->count();
    
            // Calculate the completion percentage
            $completionPercentage = $totalServices > 0 ? ($completedServices / $totalServices) * 100 : 0;
            $job->totalServices = $totalServices;
            $job->completedServices = $completedServices;
            $job->completionPercentage = $completionPercentage;
            return $job;
        });

        return view('vehicle-jobs', ['completedVehicleJobs' => $this->completedVehicleJobs]);
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
            'cars' => $cars,
            'vehicleJobs' => $this->vehicleJobs,
            'completedVehicleJobs' => $this->completedVehicleJobs,
        ]);
    }
}
