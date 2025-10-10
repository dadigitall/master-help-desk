<?php

namespace App\Livewire\Companies;

use Livewire\Component;
use App\Models\Company;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Validator;

class CompanyCreate extends Component
{
    use AuthorizesRequests;

    public $showModal = false;
    public $name = '';
    public $email = '';
    public $phone = '';
    public $address = '';
    public $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:companies,email',
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:500',
        'is_active' => 'boolean',
    ];

    protected $messages = [
        'name.required' => 'The company name is required.',
        'name.max' => 'The company name may not be greater than 255 characters.',
        'email.required' => 'The email address is required.',
        'email.email' => 'Please enter a valid email address.',
        'email.max' => 'The email may not be greater than 255 characters.',
        'email.unique' => 'This email address is already in use.',
        'phone.max' => 'The phone number may not be greater than 20 characters.',
        'address.max' => 'The address may not be greater than 500 characters.',
    ];

    protected $listeners = [
        'openCreateCompanyModal' => 'openModal',
    ];

    public function openModal()
    {
        $this->authorize('companies.create');
        
        $this->reset();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset();
        $this->resetErrorBag();
    }

    public function save()
    {
        $this->authorize('companies.create');

        $validated = $this->validate();

        try {
            Company::create($validated);

            $this->dispatch('companyCreated');
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Company created successfully!'
            ]);

            $this->closeModal();
        } catch (\Exception $e) {
            $this->addError('create', 'Failed to create company. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.companies.company-create');
    }
}
