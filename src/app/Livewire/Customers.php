<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Customer;
use Livewire\WithPagination;

class Customers extends Component
{
    use WithPagination;

    public $search;
    // public $NIC, $name, $email, $phone, $address;
    public $confirmingCustomerDeletion = false;
    public $confirmingCustomerAddition = false;
    public $password;
    public $customer = [
        'NIC' => '',
        'name' => '',
        'email' => '',
        'phone' => '',
        'address' => '',
    ];
    protected $queryString = [
        'search' => ['except' => '']
    ];
    protected $rules = [
        'customer.NIC' => 'required|string|max:20',
        'customer.name' => 'required|string|max:255',
        'customer.email' => 'required|email|max:255',
        'customer.phone' => 'required|string|max:15',
        'customer.address' => 'required|string|max:255',
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
        $this->reset('customer');
        $this->confirmingCustomerAddition = true;
    }
    public function cancelAddModel()
    {
        $this->confirmingCustomerAddition = false;
    }
    public function saveCustomer()
    {
        if (isset($this->customer['id'])) {
            $validated = $this->validate($this->rules);
            $customerData = $validated['customer'];
            Customer::where('id', $this->customer['id'])->update($customerData);
            session()->flash('message', 'Customer updated successfully');
        } else {
            $this->password = $this->generateRandomPassword();
            $validated = $this->validate($this->rules);
            $customerData = $validated['customer'];
            $customerData['password'] = bcrypt($this->password);
            Customer::create($customerData);
            session()->flash('message', 'New Customer Added successfully');
        }
        $this->confirmingCustomerAddition = false;
    }

    public function confirmCustomerEditing(Customer $customer)
    {
        $this->customer = [
            'id' => $customer->id,
            'NIC' => $customer->NIC,
            'name' => $customer->name,
            'email' => $customer->email,
            'phone' => $customer->phone,
            'address' => $customer->address,
        ];
        $this->confirmingCustomerAddition = true;
    }

    protected function generateRandomPassword($length = 8)
    {
        return bin2hex(random_bytes($length / 2));
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $customers = Customer::query()
            ->where('email', 'like', "%{$this->search}%")
            ->paginate(2);

        return view(
            'livewire.customers',
            [
                'customers' => $customers
            ]
        );
    }
}
