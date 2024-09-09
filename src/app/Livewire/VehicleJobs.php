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
    public $carId;
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

    public function confirmJobAddition($id)
    {
        $this->carId = $id;
        $this->confirmingJobAddition = true;
    }

    public function confirmJobView(VehicleJob $vehicleJob)
    {

        $this->jobServices = $vehicleJob->services()->withPivot('status')->get();
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

    public function saveVehicleJob()
    {
        $this->validate([
            'job.wash_type' => 'required',
            'job.interior_cleaning' => 'required',
            'selected_services' => 'required|array|min:1',
        ]);

        DB::beginTransaction(); 

        try {
            $vehicleJob = VehicleJob::create([
                'status' => 'pending',
                'car_id' => $this->carId,
                'is_deleted' => 0,
            ]);

            $totalDuration = 0;

            if ($this->selected_services) {
                $services = Service::whereIn('id', $this->selected_services)->get();
                foreach ($services as $service) {
                    $vehicleJob->services()->attach($service->id);
                    $totalDuration += $service->time_duration_minutes; 
                }
            }

            if ($this->job['wash_type']) {
                $washService = Service::where('name', $this->job['wash_type'])->first();
                if ($washService) {
                    $vehicleJob->services()->attach($washService->id);
                    $totalDuration += $washService->time_duration_minutes;
                }
            }

            if ($this->job['interior_cleaning']) {
                $interiorService = Service::where('name', $this->job['interior_cleaning'])->first();
                if ($interiorService) {
                    $vehicleJob->services()->attach($interiorService->id);
                    $totalDuration += $interiorService->time_duration_minutes;
                }
            }
            $vehicleJob->estimated_duration = $totalDuration;
            $vehicleJob->save();

            DB::commit();

            $this->reset(['job', 'selected_services', 'confirmingJobAddition']);

            session()->flash('message', 'Vehicle job created successfully!');

            
            $this->confirmingJobAddition = false;
        } catch (\Exception $e) {
            DB::rollBack();

            
            session()->flash('error', 'An error occurred while creating the vehicle job.');
        }
    }
    public function updateJobServiceStatuses()
    {
        if (!$this->vehicleJob) {
            session()->flash('error', 'Vehicle Job not found.');
            return;
        }

        $hasPendingServices = false;

        foreach ($this->serviceStatuses as $serviceId => $status) {
            if ($status === 'pending') {
                $hasPendingServices = true;
            }

            $this->vehicleJob->services()->updateExistingPivot($serviceId, ['status' => $status]);
        }

        if (!$hasPendingServices) {
            $this->vehicleJob->update(['status' => 'completed']);

            $carId = $this->vehicleJob->car_id;
            $car = Car::find($carId);
            $customer = Customer::find($car->customer_id);

            if ($customer && $customer->email) {
                Mail::to($customer->email)->send(new JobCompletedMail($this->vehicleJob, $customer));
            }

            session()->flash('message', 'Service completed email sent successfully.');
        } else {
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
            $totalServices = $job->services->count();

            $completedServices = $job->services->where('pivot.status', 'completed')->count();

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
        if (!$this->search) {
            return view('livewire.vehicle-jobs', [
                'cars' => collect([]) 
            ]);
        }

        $customerQuery = Customer::query();

        if ($this->search) {
            $customerQuery->where(function ($q) {
                $q->where('email', 'like', "%{$this->search}%")
                    ->orWhere('NIC', 'like', "%{$this->search}%");
            });
        }

        $customer = $customerQuery->first();

        $carQuery = Car::query();

        if ($customer) {
            $carQuery->where('customer_id', $customer->id);
        } else {
            return view('livewire.vehicle-jobs', [
                'cars' => collect([]) 
            ]);
        }

        $cars = $carQuery->paginate(4);
        return view('livewire.vehicle-jobs', [
            'cars' => $cars,
            'vehicleJobs' => $this->vehicleJobs,
            'completedVehicleJobs' => $this->completedVehicleJobs,
        ]);
    }
}
