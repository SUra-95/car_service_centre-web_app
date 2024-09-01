<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Customer;
use Livewire\WithPagination;

class Customers extends Component
{
    use WithPagination;

    public $search;
    public $confirmingCustomerDeletion = false;
    protected $queryString = [
        'search' => ['except' => '']
    ];

    public function confirmCustomerDeletion( $id ){
        $this->confirmingCustomerDeletion = $id;
    }

    public function deleteCustomer(Customer $customer){
        $customer->delete();
        $this->confirmingCustomerDeletion = false;
    }
    public function cancelDeleteModel(){
        $this->confirmingCustomerDeletion = false;
    }
    


    public function render()
    {
        $customers = Customer::latest()
            // ->when($this->search, function($query){
            //     return $query->where(function($query){
            //         $query->where('name', 'like', '%'.$this->search.'%')
            //         ->orWhere('email', 'like', '%'.$this->search.'%');
            //     });

            // })
            ->paginate(2);

            return view('livewire.customers', 
            [
                'customers' => Customer::latest()
                    ->where('email', 'like', "%{$this->search}%")
                    ->paginate(2)
            ]
            );
    }
}
