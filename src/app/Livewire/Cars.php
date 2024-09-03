<?php

namespace App\Livewire;

use App\Models\Car;
use App\Models\Customer;
use Livewire\Component;
use Livewire\WithPagination;

class Cars extends Component
{
    use WithPagination;

    public $search;
    public $confirmingCarDeletion = false;
    public $confirmingCarAddition = false;
    public $password;
    public $customers;
    public $filter;
    public $car = [
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
    }

    public function confirmCarDeletion($id)
    {
        $this->confirmingCarDeletion = $id;
    }
    public function deleteCar(Car $car)
    {
        $car->delete();
        $this->confirmingCarDeletion = false;
        session()->flash('message', 'Car deleted successfully');
    }
    public function cancelDeleteModel()
    {
        $this->confirmingCarDeletion = false;
    }
    public function confirmCarAddition()
    {
        $this->reset('car');
        $this->confirmingCarAddition = true;
    }
    public function cancelAddModel()
    {
        $this->confirmingCarAddition = false;
    }
    public function saveCar()
    {
        if (isset($this->car['id'])) {
            $validated = $this->validate($this->rules);
            $carData = $validated['car'];
            Car::where('id', $this->car['id'])->update($carData);
            session()->flash('message', 'Car updated successfully');
        } else {
            $validated = $this->validate($this->rules);
            $carData = $validated['car'];
            $carData['is_deleted'] = 0;
            Car::create($carData);
            session()->flash('message', 'New Car Added successfully');
        }
        $this->confirmingCarAddition = false;
    }

    public function confirmCarEditing(Car $car)
    {
        $this->car = [
            'id' => $car->id,
            'registration_number' => $car->registration_number,
            'model' => $car->model,
            'fuel_type' => $car->fuel_type,
            'transmission' => $car->transmission,
            'customer_id' => $car->customer_id,
        ];
        $this->confirmingCarAddition = true;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function handleCustomerChange($customerId)
    {
        $this->filter = $customerId;
    }

    public function render()
    {
        $query = Car::query();

        if ($this->filter) {
            $query->where('customer_id', $this->filter);
        }
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('model', 'like', "%{$this->search}%")
                    ->orWhere('registration_number', 'like', "%{$this->search}%");
            });
        }
        $cars = $query->paginate(4);
        return view(
            'livewire.cars',
            [
                'cars' => $cars
            ]
        );
    }
}
