<?php

namespace App\Livewire;

use App\Models\Car;
use Livewire\Component;
use Livewire\WithPagination;

class Cars extends Component
{
    use WithPagination;

    public $search;
    public $confirmingCarDeletion = false;
    public $confirmingCarAddition = false;
    public $password;
    public $car = [
        'registration_number' => '',
        'model' => '',
        'fuel_type' => '',
        'transmission' => '',
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
        ];
        $this->confirmingCarAddition = true;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $cars = Car::query()
            ->where('model', 'like', "%{$this->search}%")
            ->paginate(2);

        return view(
            'livewire.cars',
            [
                'cars' => $cars
            ]
        );
    }
}
