<?php

namespace App\Livewire\Companies;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Company;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CompanyIndex extends Component
{
    use WithPagination, AuthorizesRequests;

    public $search = '';
    public $perPage = 10;
    public $sortBy = 'name';
    public $sortDirection = 'asc';
    public $showInactive = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'sortBy' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
        'showInactive' => ['except' => false],
    ];

    protected $listeners = [
        'companyCreated' => '$refresh',
        'companyUpdated' => '$refresh',
        'companyDeleted' => '$refresh',
    ];

    public function mount()
    {
        $this->authorize('companies.view');
    }

    public function render()
    {
        $this->authorize('companies.view');

        $companies = Company::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%');
            })
            ->when(!$this->showInactive, function ($query) {
                $query->where('is_active', true);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.companies.company-index', [
            'companies' => $companies,
        ]);
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleActive(Company $company)
    {
        $this->authorize('companies.edit');

        $company->update([
            'is_active' => !$company->is_active,
        ]);

        $this->dispatch('companyUpdated');
    }

    public function deleteCompany(Company $company)
    {
        $this->authorize('companies.delete');

        if ($company->projects()->exists()) {
            $this->addError('delete', 'Cannot delete company with existing projects.');
            return;
        }

        $company->delete();
        $this->dispatch('companyDeleted');
    }

    public function getCanCreateProperty()
    {
        return auth()->user()->can('companies.create');
    }

    public function getCanEditProperty()
    {
        return auth()->user()->can('companies.edit');
    }

    public function getCanDeleteProperty()
    {
        return auth()->user()->can('companies.delete');
    }
}
