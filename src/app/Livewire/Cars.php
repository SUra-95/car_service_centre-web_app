<?php

namespace App\Livewire;

use App\Models\Car;
use Livewire\Component;
use App\Models\Customer;
use Livewire\WithPagination;

class Cars extends Component
{
    use WithPagination;

    public $search;
    public $confirmingCustomerDeletion = false;
    public $confirmingCustomerAddition = false;
    public $password;
    public $car = [
        'registration_number' => '',
        'model' => '',
        'fuel_type' => '',
        'transmission' => '',
        'is_deleted' => 0
    ];
    protected $queryString = [
        'search' => ['except' => '']
    ];
    protected $rules = [
        'car.registration_number' => 'required|string|max:20',
        'car.model' => 'required|string|max:255',
        'car.fuel_type' => 'required|max:255',
        'car.transmission' => 'required|string|max:15',
    ];

    public function confirmCustomerDeletion($id)
    {
        $this->confirmingCustomerDeletion = $id;
    }
    public function deleteCustomer(Customer $customer)
    {
        $customer->delete();
        $this->confirmingCustomerDeletion = false;
        session()->flash('message', 'Customer deleted successfully');

    }
    public function cancelDeleteModel()
    {
        $this->confirmingCustomerDeletion = false;
    }
    public function confirmCustomerAddition()
    {
        $this->reset('car');
        $this->confirmingCustomerAddition = true;
    }
    public function cancelAddModel()
    {
        $this->confirmingCustomerAddition = false;
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
        $this->confirmingCustomerAddition = false;
    }

    public function confirmCustomerEditing(Car $car)
    {
        $this->car = [
            'id' => $car->id,
            'registration_number' => $car->registration_number,
            'model' => $car->model,
            'fuel_type' => $car->fuel_type,
            'transmission' => $car->transmission,
        ];
        $this->confirmingCustomerAddition = true;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $customers = Car::query()
            // ->where('email', 'like', "%{$this->search}%")
            ->paginate(2);

        return view(
            'livewire.cars',
            [
                'cars' => $customers
            ]
        );
    }
}
