<?php

namespace App\Livewire\Companies;

use Livewire\Component;
use App\Models\Company;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CompanyEdit extends Component
{
    use AuthorizesRequests;

    public $showModal = false;
    public $company;
    public $name = '';
    public $email = '';
    public $phone = '';
    public $address = '';
    public $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:companies,email,{company}',
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
        'openEditCompanyModal' => 'openModal',
    ];

    public function openModal($companyId)
    {
        $this->authorize('companies.edit');

        $this->company = Company::findOrFail($companyId);
        $this->name = $this->company->name;
        $this->email = $this->company->email;
        $this->phone = $this->company->phone;
        $this->address = $this->company->address;
        $this->is_active = $this->company->active;
        
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
        $this->authorize('companies.edit');

        $validated = $this->validate();

        try {
            $this->company->update($validated);

            $this->dispatch('companyUpdated');
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Company updated successfully!'
            ]);

            $this->closeModal();
        } catch (\Exception $e) {
            $this->addError('update', 'Failed to update company. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.companies.company-edit');
    }
}
