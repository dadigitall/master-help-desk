<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use App\Models\Company;
use App\Models\Role;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserIndex extends Component
{
    use WithPagination;

    // Propriétés de recherche et filtrage
    public $search = '';
    public $role = '';
    public $status = '';
    public $company = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $sortBy = 'created_at';
    public $sortOrder = 'desc';

    // Propriétés pour la création/modification rapide
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    public $showPasswordModal = false;
    public $selectedUser = null;
    public $selectedUsers = [];

    // Propriétés du formulaire
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $roles = [];
    public $companies = [];
    public $permissions = [];
    public $send_welcome_email = false;
    public $send_password_email = false;

    // Propriétés pour le bulk import
    public $showImportModal = false;
    public $csvFile = null;
    public $importPreview = [];
    public $importErrors = [];

    protected $paginationTheme = 'bootstrap';
    protected $queryString = [
        'search' => ['except' => ''],
        'role' => ['except' => ''],
        'status' => ['except' => ''],
        'company' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortOrder' => ['except' => 'desc'],
    ];

    protected $listeners = [
        'userUpdated' => '$refresh',
        'userDeleted' => '$refresh',
        'refreshUsers' => '$refresh',
    ];

    public function mount()
    {
        $this->authorize('viewAny', User::class);
    }

    public function render()
    {
        $query = User::with(['companies', 'roles', 'permissions'])
            ->withCount(['tickets', 'comments', 'companies']);

        // Appliquer les filtres
        $this->applyFilters($query);

        // Appliquer le tri
        $this->applySorting($query);

        $users = $query->paginate(15);

        // Données pour les filtres
        $roles = Role::orderBy('name')->get();
        $companies = Company::orderBy('name')->get();

        // Statistiques
        $stats = $this->getStats();

        return view('livewire.admin.users.user-index', compact(
            'users',
            'roles',
            'companies',
            'stats'
        ));
    }

    protected function applyFilters($query)
    {
        // Recherche
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'LIKE', "%{$this->search}%")
                  ->orWhere('email', 'LIKE', "%{$this->search}%")
                  ->orWhere('id', 'LIKE', "%{$this->search}%");
            });
        }

        // Filtre par rôle
        if ($this->role) {
            $query->whereHas('roles', function ($q) {
                $q->where('name', $this->role);
            });
        }

        // Filtre par statut
        if ($this->status) {
            if ($this->status === 'verified') {
                $query->whereNotNull('email_verified_at');
            } elseif ($this->status === 'unverified') {
                $query->whereNull('email_verified_at');
            } elseif ($this->status === 'active_today') {
                $query->whereDate('last_login_at', today());
            }
        }

        // Filtre par entreprise
        if ($this->company) {
            $query->whereHas('companies', function ($q) {
                $q->where('companies.id', $this->company);
            });
        }

        // Filtre par date
        if ($this->dateFrom) {
            $query->where('created_at', '>=', $this->dateFrom . ' 00:00:00');
        }

        if ($this->dateTo) {
            $query->where('created_at', '<=', $this->dateTo . ' 23:59:59');
        }
    }

    protected function applySorting($query)
    {
        $allowedSorts = ['name', 'email', 'created_at', 'last_login_at', 'tickets_count'];
        
        if (in_array($this->sortBy, $allowedSorts)) {
            $query->orderBy($this->sortBy, $this->sortOrder);
        }
    }

    protected function getStats()
    {
        return [
            'total' => User::count(),
            'verified' => User::whereNotNull('email_verified_at')->count(),
            'active_today' => User::whereDate('last_login_at', today())->count(),
            'new_this_month' => User::whereMonth('created_at', now()->month)->count(),
            'selected_count' => count($this->selectedUsers),
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedRole()
    {
        $this->resetPage();
    }

    public function updatedStatus()
    {
        $this->resetPage();
    }

    public function updatedCompany()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortOrder = $this->sortOrder === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortOrder = 'asc';
        }
        
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset([
            'search', 'role', 'status', 'company', 'dateFrom', 'dateTo',
            'sortBy', 'sortOrder'
        ]);
        $this->sortBy = 'created_at';
        $this->sortOrder = 'desc';
        $this->resetPage();
    }

    // Gestion de la sélection
    public function selectAll()
    {
        $users = User::pluck('id')->toArray();
        $this->selectedUsers = $users;
    }

    public function deselectAll()
    {
        $this->selectedUsers = [];
    }

    public function toggleUserSelection($userId)
    {
        if (in_array($userId, $this->selectedUsers)) {
            $this->selectedUsers = array_diff($this->selectedUsers, [$userId]);
        } else {
            $this->selectedUsers[] = $userId;
        }
    }

    // Actions en masse
    public function bulkDelete()
    {
        $this->authorize('delete users');

        if (empty($this->selectedUsers)) {
            $this->dispatch('showNotification', [
                'type' => 'error',
                'message' => 'Veuillez sélectionner au moins un utilisateur.'
            ]);
            return;
        }

        // Empêcher la suppression de soi-même
        if (in_array(auth()->id(), $this->selectedUsers)) {
            $this->dispatch('showNotification', [
                'type' => 'error',
                'message' => 'Vous ne pouvez pas supprimer votre propre compte.'
            ]);
            return;
        }

        try {
            $count = User::whereIn('id', $this->selectedUsers)->delete();
            
            Log::warning('Suppression en masse d\'utilisateurs', [
                'count' => $count,
                'user_ids' => $this->selectedUsers,
                'admin_id' => auth()->id(),
            ]);

            $this->dispatch('showNotification', [
                'type' => 'success',
                'message' => "{$count} utilisateur(s) supprimé(s) avec succès."
            ]);

            $this->selectedUsers = [];
            $this->dispatch('refreshUsers');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression en masse', [
                'error' => $e->getMessage(),
                'user_ids' => $this->selectedUsers,
            ]);

            $this->dispatch('showNotification', [
                'type' => 'error',
                'message' => 'Une erreur est survenue lors de la suppression.'
            ]);
        }
    }

    public function bulkActivate()
    {
        $this->authorize('update users');

        if (empty($this->selectedUsers)) {
            $this->dispatch('showNotification', [
                'type' => 'error',
                'message' => 'Veuillez sélectionner au moins un utilisateur.'
            ]);
            return;
        }

        try {
            $count = User::whereIn('id', $this->selectedUsers)
                ->update(['email_verified_at' => now()]);
            
            Log::info('Activation en masse d\'utilisateurs', [
                'count' => $count,
                'user_ids' => $this->selectedUsers,
                'admin_id' => auth()->id(),
            ]);

            $this->dispatch('showNotification', [
                'type' => 'success',
                'message' => "{$count} utilisateur(s) activé(s) avec succès."
            ]);

            $this->selectedUsers = [];
            $this->dispatch('refreshUsers');

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'activation en masse', [
                'error' => $e->getMessage(),
                'user_ids' => $this->selectedUsers,
            ]);

            $this->dispatch('showNotification', [
                'type' => 'error',
                'message' => 'Une erreur est survenue lors de l\'activation.'
            ]);
        }
    }

    public function bulkDeactivate()
    {
        $this->authorize('update users');

        if (empty($this->selectedUsers)) {
            $this->dispatch('showNotification', [
                'type' => 'error',
                'message' => 'Veuillez sélectionner au moins un utilisateur.'
            ]);
            return;
        }

        // Empêcher la désactivation de soi-même
        if (in_array(auth()->id(), $this->selectedUsers)) {
            $this->dispatch('showNotification', [
                'type' => 'error',
                'message' => 'Vous ne pouvez pas désactiver votre propre compte.'
            ]);
            return;
        }

        try {
            $count = User::whereIn('id', $this->selectedUsers)
                ->update(['email_verified_at' => null]);
            
            Log::info('Désactivation en masse d\'utilisateurs', [
                'count' => $count,
                'user_ids' => $this->selectedUsers,
                'admin_id' => auth()->id(),
            ]);

            $this->dispatch('showNotification', [
                'type' => 'success',
                'message' => "{$count} utilisateur(s) désactivé(s) avec succès."
            ]);

            $this->selectedUsers = [];
            $this->dispatch('refreshUsers');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la désactivation en masse', [
                'error' => $e->getMessage(),
                'user_ids' => $this->selectedUsers,
            ]);

            $this->dispatch('showNotification', [
                'type' => 'error',
                'message' => 'Une erreur est survenue lors de la désactivation.'
            ]);
        }
    }

    // Modal de création
    public function openCreateModal()
    {
        $this->authorize('create users');
        
        $this->reset([
            'name', 'email', 'password', 'password_confirmation',
            'roles', 'companies', 'permissions', 'send_welcome_email'
        ]);
        
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetValidation();
    }

    public function createUser()
    {
        $this->authorize('create users');

        $validator = Validator::make([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            $this->validate();
            return;
        }

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'email_verified_at' => $this->send_welcome_email ? now() : null,
            ]);

            if (!empty($this->roles)) {
                $user->roles()->sync($this->roles);
            }

            if (!empty($this->permissions)) {
                $user->permissions()->sync($this->permissions);
            }

            if (!empty($this->companies)) {
                $user->companies()->sync($this->companies);
            }

            if ($this->send_welcome_email) {
                $user->sendWelcomeNotification();
            }

            DB::commit();

            Log::info('Utilisateur créé via modal rapide', [
                'user_id' => $user->id,
                'email' => $user->email,
                'admin_id' => auth()->id(),
            ]);

            $this->dispatch('showNotification', [
                'type' => 'success',
                'message' => 'Utilisateur créé avec succès.'
            ]);

            $this->closeCreateModal();
            $this->dispatch('refreshUsers');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création rapide d\'utilisateur', [
                'error' => $e->getMessage(),
                'data' => [
                    'name' => $this->name,
                    'email' => $this->email,
                ],
            ]);

            $this->dispatch('showNotification', [
                'type' => 'error',
                'message' => 'Une erreur est survenue lors de la création.'
            ]);
        }
    }

    // Modal d'édition
    public function openEditModal(User $user)
    {
        $this->authorize('update', $user);

        $this->selectedUser = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->roles = $user->roles->pluck('id')->toArray();
        $this->companies = $user->companies->pluck('id')->toArray();
        $this->permissions = $user->permissions->pluck('id')->toArray();
        
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->selectedUser = null;
        $this->resetValidation();
    }

    public function updateUser()
    {
        if (!$this->selectedUser) {
            return;
        }

        $this->authorize('update', $this->selectedUser);

        $validator = Validator::make([
            'name' => $this->name,
            'email' => $this->email,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->selectedUser->id),
            ],
        ]);

        if ($validator->fails()) {
            $this->validate();
            return;
        }

        try {
            DB::beginTransaction();

            $this->selectedUser->update([
                'name' => $this->name,
                'email' => $this->email,
            ]);

            $this->selectedUser->roles()->sync($this->roles);
            $this->selectedUser->permissions()->sync($this->permissions);
            $this->selectedUser->companies()->sync($this->companies);

            DB::commit();

            Log::info('Utilisateur modifié via modal rapide', [
                'user_id' => $this->selectedUser->id,
                'email' => $this->selectedUser->email,
                'admin_id' => auth()->id(),
            ]);

            $this->dispatch('showNotification', [
                'type' => 'success',
                'message' => 'Utilisateur mis à jour avec succès.'
            ]);

            $this->closeEditModal();
            $this->dispatch('refreshUsers');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la modification rapide d\'utilisateur', [
                'error' => $e->getMessage(),
                'user_id' => $this->selectedUser->id,
            ]);

            $this->dispatch('showNotification', [
                'type' => 'error',
                'message' => 'Une erreur est survenue lors de la modification.'
            ]);
        }
    }

    // Modal de suppression
    public function openDeleteModal(User $user)
    {
        $this->authorize('delete', $user);
        
        if ($user->id === auth()->id()) {
            $this->dispatch('showNotification', [
                'type' => 'error',
                'message' => 'Vous ne pouvez pas supprimer votre propre compte.'
            ]);
            return;
        }

        $this->selectedUser = $user;
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->selectedUser = null;
    }

    public function deleteUser()
    {
        if (!$this->selectedUser) {
            return;
        }

        $this->authorize('delete', $this->selectedUser);

        try {
            $userName = $this->selectedUser->name;
            $this->selectedUser->delete();

            Log::warning('Utilisateur supprimé via modal', [
                'user_id' => $this->selectedUser->id,
                'email' => $this->selectedUser->email,
                'admin_id' => auth()->id(),
            ]);

            $this->dispatch('showNotification', [
                'type' => 'success',
                'message' => "L'utilisateur {$userName} a été supprimé."
            ]);

            $this->closeDeleteModal();
            $this->dispatch('refreshUsers');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression via modal', [
                'error' => $e->getMessage(),
                'user_id' => $this->selectedUser->id,
            ]);

            $this->dispatch('showNotification', [
                'type' => 'error',
                'message' => 'Une erreur est survenue lors de la suppression.'
            ]);
        }
    }

    // Modal de réinitialisation de mot de passe
    public function openPasswordModal(User $user)
    {
        $this->authorize('update', $user);
        
        $this->selectedUser = $user;
        $this->password = '';
        $this->password_confirmation = '';
        $this->send_password_email = false;
        
        $this->showPasswordModal = true;
    }

    public function closePasswordModal()
    {
        $this->showPasswordModal = false;
        $this->selectedUser = null;
        $this->resetValidation();
    }

    public function resetPassword()
    {
        if (!$this->selectedUser) {
            return;
        }

        $this->authorize('update', $this->selectedUser);

        $validator = Validator::make([
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
        ], [
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            $this->validate();
            return;
        }

        try {
            $this->selectedUser->update([
                'password' => Hash::make($this->password),
            ]);

            if ($this->send_password_email) {
                $this->selectedUser->sendPasswordResetNotification();
            }

            Log::info('Mot de passe réinitialisé via modal', [
                'user_id' => $this->selectedUser->id,
                'email' => $this->selectedUser->email,
                'admin_id' => auth()->id(),
            ]);

            $this->dispatch('showNotification', [
                'type' => 'success',
                'message' => 'Mot de passe réinitialisé avec succès.'
            ]);

            $this->closePasswordModal();

        } catch (\Exception $e) {
            Log::error('Erreur lors de la réinitialisation du mot de passe', [
                'error' => $e->getMessage(),
                'user_id' => $this->selectedUser->id,
            ]);

            $this->dispatch('showNotification', [
                'type' => 'error',
                'message' => 'Une erreur est survenue lors de la réinitialisation.'
            ]);
        }
    }

    // Toggle statut
    public function toggleStatus(User $user)
    {
        $this->authorize('update', $user);

        if ($user->id === auth()->id()) {
            $this->dispatch('showNotification', [
                'type' => 'error',
                'message' => 'Vous ne pouvez pas modifier votre propre statut.'
            ]);
            return;
        }

        try {
            $user->update([
                'email_verified_at' => $user->email_verified_at ? null : now(),
            ]);

            $status = $user->email_verified_at ? 'activé' : 'désactivé';

            Log::info('Statut utilisateur modifié via toggle', [
                'user_id' => $user->id,
                'email' => $user->email,
                'status' => $status,
                'admin_id' => auth()->id(),
            ]);

            $this->dispatch('showNotification', [
                'type' => 'success',
                'message' => "Utilisateur {$status} avec succès."
            ]);

            $this->dispatch('refreshUsers');

        } catch (\Exception $e) {
            Log::error('Erreur lors du changement de statut', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);

            $this->dispatch('showNotification', [
                'type' => 'error',
                'message' => 'Une erreur est survenue lors du changement de statut.'
            ]);
        }
    }

    // Export CSV
    public function export()
    {
        $this->authorize('viewAny', User::class);
        
        return redirect()->route('admin.users.export', [
            'search' => $this->search,
            'role' => $this->role,
            'status' => $this->status,
            'company' => $this->company,
            'date_from' => $this->dateFrom,
            'date_to' => $this->dateTo,
        ]);
    }
}
