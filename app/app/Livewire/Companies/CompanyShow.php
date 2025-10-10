<?php

namespace App\Livewire\Companies;

use Livewire\Component;
use App\Models\Company;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CompanyShow extends Component
{
    use AuthorizesRequests;

    public $showModal = false;
    public $company;

    protected $listeners = [
        'openViewCompanyModal' => 'openModal',
    ];

    public function openModal($companyId)
    {
        $this->authorize('companies.view');

        $this->company = Company::with(['projects' => function($query) {
            $query->with('user')->orderBy('name');
        }])->findOrFail($companyId);
        
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset();
    }

    public function render()
    {
        return view('livewire.companies.company-show');
    }
}
