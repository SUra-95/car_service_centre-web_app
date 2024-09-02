<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Customer;
use Livewire\WithPagination;

class Customers extends Component
{
    use WithPagination;

    public $search;
    public $customer;
    public $NIC, $name, $email, $phone, $address;
    public $confirmingCustomerDeletion = false;
    public $confirmingCustomerAddition = false;
    public $password;
    protected $queryString = [
        'search' => ['except' => '']
    ];
    protected $rules = [
        'NIC' => 'required|min:10|max:15',
        'name' => 'required|min:3|max:50',
        'email' => 'required|email',
        'password' => 'required|min:8',
        'phone' => 'required|regex:/^\d{10}$/',
        'address' => 'required|string|max:255',
    ];

    public function confirmCustomerDeletion( $id ){
        $this->confirmingCustomerDeletion = $id;
    }
    public function deleteCustomer(Customer $customer){
        dd('dwwwwwwwwwwwww');
        $customer->delete();
        $this->confirmingCustomerDeletion = false;
    }
    public function cancelDeleteModel(){
        $this->confirmingCustomerDeletion = false;
    }
    public function confirmCustomerAddition(){
        $this->confirmingCustomerAddition = true;
    }
    public function cancelAddModel(){
        $this->confirmingCustomerAddition = false;

    }
    public function saveCustomer(){
        // dd('awaa');
        $this->password = $this->generateRandomPassword();
        $validated = $this->validate($this->rules);
        Customer::create($validated);
        $this->confirmingCustomerAddition = false;
    }

    protected function generateRandomPassword($length = 8)
    {
        return bin2hex(random_bytes($length / 2));
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
                'customers' => Customer::query()
                    ->where('email', 'like', "%{$this->search}%")
                    ->paginate(2)
            ]
            );
    }
}
