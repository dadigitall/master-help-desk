<?php

namespace App\Livewire\Projects;

use Livewire\Component;
use App\Models\Project;
use App\Models\Company;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProjectCreate extends Component
{
    use AuthorizesRequests;

    public $showModal = false;
    public $name = '';
    public $description = '';
    public $prefix = '';
    public $color = '#3B82F6';
    public $company_id = '';
    public $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'prefix' => 'required|string|max:10|unique:projects,prefix',
        'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
        'company_id' => 'required|exists:companies,id',
        'is_active' => 'boolean',
    ];

    protected $messages = [
        'name.required' => 'The project name is required.',
        'name.max' => 'The project name may not be greater than 255 characters.',
        'description.max' => 'The description may not be greater than 1000 characters.',
        'prefix.required' => 'The project prefix is required.',
        'prefix.max' => 'The prefix may not be greater than 10 characters.',
        'prefix.unique' => 'This prefix is already in use.',
        'color.required' => 'The project color is required.',
        'color.regex' => 'Please select a valid color.',
        'company_id.required' => 'Please select a company.',
        'company_id.exists' => 'The selected company is invalid.',
    ];

    protected $listeners = [
        'openCreateProjectModal' => 'openModal',
    ];

    public function openModal()
    {
        $this->authorize('projects.create');

        $this->reset();
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset();
        $this->resetErrorBag();
    }

    public function generatePrefix()
    {
        if (empty($this->name)) {
            return;
        }

        // Generate prefix from name (first 3-4 letters, uppercase)
        $words = preg_split('/\s+/', $this->name);
        if (count($words) >= 2) {
            $prefix = '';
            foreach ($words as $word) {
                $prefix .= strtoupper(substr($word, 0, 1));
                if (strlen($prefix) >= 3) break;
            }
        } else {
            $prefix = strtoupper(substr($this->name, 0, 3));
        }

        // Check if prefix exists, if so, add number
        $originalPrefix = $prefix;
        $counter = 1;
        while (Project::where('prefix', $prefix)->exists()) {
            $prefix = $originalPrefix . $counter;
            $counter++;
        }

        $this->prefix = $prefix;
    }

    public function randomColor()
    {
        $colors = [
            '#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6',
            '#EC4899', '#14B8A6', '#F97316', '#6366F1', '#84CC16',
            '#06B6D4', '#A855F7', '#0EA5E9', '#22C55E', '#F43F5E'
        ];
        $this->color = $colors[array_rand($colors)];
    }

    public function save()
    {
        $this->authorize('projects.create');

        $validated = $this->validate();

        try {
            $project = Project::create([
                ...$validated,
                'user_id' => auth()->id(),
            ]);

            $this->dispatch('projectCreated');
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Project created successfully!'
            ]);

            $this->closeModal();
        } catch (\Exception $e) {
            $this->addError('create', 'Failed to create project. Please try again.');
        }
    }

    public function updatedName()
    {
        $this->generatePrefix();
    }

    public function getCompaniesProperty()
    {
        return auth()->user()->companies()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        return view('livewire.projects.project-create', [
            'companies' => $this->companies,
        ]);
    }
}
