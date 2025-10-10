<div>
    <!-- En-tête avec statistiques -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">Gestion des Utilisateurs</h4>
                    <p class="text-muted mb-0">Administration des comptes utilisateurs</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary" wire:click="export">
                        <i class="fas fa-download me-1"></i> Exporter
                    </button>
                    <button type="button" class="btn btn-primary" wire:click="openCreateModal">
                        <i class="fas fa-plus me-1"></i> Nouvel Utilisateur
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cartes de statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['total'] }}</h4>
                            <p class="mb-0">Total Utilisateurs</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['verified'] }}</h4>
                            <p class="mb-0">Vérifiés</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['active_today'] }}</h4>
                            <p class="mb-0">Actifs Aujourd'hui</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calendar-day fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['new_this_month'] }}</h4>
                            <p class="mb-0">Nouveaux ce mois</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-plus fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Filtres</h5>
        </div>
        <div class="card-body">
            <form wire:submit.prevent="updatedSearch">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Recherche</label>
                        <input type="text" class="form-control" id="search" wire:model.live="search" 
                               placeholder="Nom, email ou ID...">
                    </div>
                    <div class="col-md-2">
                        <label for="role" class="form-label">Rôle</label>
                        <select class="form-select" id="role" wire:model.live="role">
                            <option value="">Tous les rôles</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label">Statut</label>
                        <select class="form-select" id="status" wire:model.live="status">
                            <option value="">Tous les statuts</option>
                            <option value="verified">Vérifiés</option>
                            <option value="unverified">Non vérifiés</option>
                            <option value="active_today">Actifs aujourd'hui</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="company" class="form-label">Entreprise</label>
                        <select class="form-select" id="company" wire:model.live="company">
                            <option value="">Toutes les entreprises</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label for="dateFrom" class="form-label">Du</label>
                        <input type="date" class="form-control" id="dateFrom" wire:model.live="dateFrom">
                    </div>
                    <div class="col-md-1">
                        <label for="dateTo" class="form-label">Au</label>
                        <input type="date" class="form-control" id="dateTo" wire:model.live="dateTo">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label d-block">&nbsp;</label>
                        <button type="button" class="btn btn-outline-secondary w-100" wire:click="clearFilters">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Actions en masse -->
    @if(count($selectedUsers) > 0)
        <div class="alert alert-info d-flex justify-content-between align-items-center mb-3">
            <span>{{ $stats['selected_count'] }} utilisateur(s) sélectionné(s)</span>
            <div class="btn-group">
                <button type="button" class="btn btn-success btn-sm" wire:click="bulkActivate">
                    <i class="fas fa-check me-1"></i> Activer
                </button>
                <button type="button" class="btn btn-warning btn-sm" wire:click="bulkDeactivate">
                    <i class="fas fa-pause me-1"></i> Désactiver
                </button>
                <button type="button" class="btn btn-danger btn-sm" wire:click="bulkDelete">
                    <i class="fas fa-trash me-1"></i> Supprimer
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="deselectAll">
                    <i class="fas fa-times me-1"></i> Désélectionner
                </button>
            </div>
        </div>
    @endif

    <!-- Tableau des utilisateurs -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="40">
                                <input type="checkbox" 
                                       wire:model.live="selectAll"
                                       wire:change="selectAll()"
                                       class="form-check-input">
                            </th>
                            <th wire:click="sortBy('id')" class="cursor-pointer">
                                ID
                                @if($sortBy === 'id')
                                    <i class="fas fa-sort-{{ $sortOrder }} ms-1"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('name')" class="cursor-pointer">
                                Nom
                                @if($sortBy === 'name')
                                    <i class="fas fa-sort-{{ $sortOrder }} ms-1"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('email')" class="cursor-pointer">
                                Email
                                @if($sortBy === 'email')
                                    <i class="fas fa-sort-{{ $sortOrder }} ms-1"></i>
                                @endif
                            </th>
                            <th>Rôles</th>
                            <th>Entreprises</th>
                            <th>Statut</th>
                            <th wire:click="sortBy('last_login_at')" class="cursor-pointer">
                                Dernière connexion
                                @if($sortBy === 'last_login_at')
                                    <i class="fas fa-sort-{{ $sortOrder }} ms-1"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('tickets_count')" class="cursor-pointer">
                                Tickets
                                @if($sortBy === 'tickets_count')
                                    <i class="fas fa-sort-{{ $sortOrder }} ms-1"></i>
                                @endif
                            </th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>
                                    <input type="checkbox" 
                                           wire:model.live="selectedUsers"
                                           value="{{ $user->id }}"
                                           class="form-check-input">
                                </td>
                                <td>{{ $user->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="fw-medium">{{ $user->name }}</div>
                                            <small class="text-muted">{{ $user->created_at->format('d/m/Y') }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="mailto:{{ $user->email }}" class="text-decoration-none">
                                        {{ $user->email }}
                                    </a>
                                </td>
                                <td>
                                    @foreach($user->roles as $role)
                                        <span class="badge bg-primary me-1">{{ $role->name }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    @if($user->companies_count > 0)
                                        <small class="text-muted">
                                            {{ $user->companies_count }} entreprise(s)
                                        </small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->email_verified_at)
                                        <span class="badge bg-success">Vérifié</span>
                                    @else
                                        <span class="badge bg-warning">Non vérifié</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->last_login_at)
                                        <small>{{ $user->last_login_at->diffForHumans() }}</small>
                                    @else
                                        <span class="text-muted">Jamais</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $user->tickets_count }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                wire:click="openEditModal('{{ $user->id }}')" 
                                                title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-info" 
                                                wire:click="openPasswordModal('{{ $user->id }}')" 
                                                title="Réinitialiser mot de passe">
                                            <i class="fas fa-key"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-{{ $user->email_verified_at ? 'warning' : 'success' }}" 
                                                wire:click="toggleStatus('{{ $user->id }}')" 
                                                title="{{ $user->email_verified_at ? 'Désactiver' : 'Activer' }}">
                                            <i class="fas fa-{{ $user->email_verified_at ? 'eye-slash' : 'eye' }}"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                wire:click="openDeleteModal('{{ $user->id }}')" 
                                                title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">Aucun utilisateur trouvé</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            {{ $users->links() }}
        </div>
    </div>

    <!-- Modal de création -->
    @if($showCreateModal)
        <div class="modal show d-block" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Nouvel Utilisateur</h5>
                        <button type="button" class="btn-close" wire:click="closeCreateModal"></button>
                    </div>
                    <form wire:submit.prevent="createUser">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Nom *</label>
                                    <input type="text" class="form-control" id="name" wire:model.live="name" required>
                                    @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="email" wire:model.live="email" required>
                                    @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="password" class="form-label">Mot de passe *</label>
                                    <input type="password" class="form-control" id="password" wire:model.live="password" required>
                                    @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="password_confirmation" class="form-label">Confirmation mot de passe *</label>
                                    <input type="password" class="form-control" id="password_confirmation" wire:model.live="password_confirmation" required>
                                    @error('password_confirmation') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Rôles</label>
                                    <div class="row">
                                        @foreach(\Spatie\Permission\Models\Role::orderBy('name')->get() as $role)
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" 
                                                           id="role_{{ $role->id }}" 
                                                           wire:model.live="roles" 
                                                           value="{{ $role->id }}">
                                                    <label class="form-check-label" for="role_{{ $role->id }}">
                                                        {{ $role->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Entreprises</label>
                                    <div class="row">
                                        @foreach($companies as $company)
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" 
                                                           id="company_{{ $company->id }}" 
                                                           wire:model.live="companies" 
                                                           value="{{ $company->id }}">
                                                    <label class="form-check-label" for="company_{{ $company->id }}">
                                                        {{ $company->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="send_welcome_email" wire:model.live="send_welcome_email">
                                        <label class="form-check-label" for="send_welcome_email">
                                            Envoyer un email de bienvenue
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeCreateModal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Créer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal d'édition -->
    @if($showEditModal && $selectedUser)
        <div class="modal show d-block" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Modifier l'utilisateur</h5>
                        <button type="button" class="btn-close" wire:click="closeEditModal"></button>
                    </div>
                    <form wire:submit.prevent="updateUser">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="edit_name" class="form-label">Nom *</label>
                                    <input type="text" class="form-control" id="edit_name" wire:model.live="name" required>
                                    @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="edit_email" wire:model.live="email" required>
                                    @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Rôles</label>
                                    <div class="row">
                                        @foreach(\Spatie\Permission\Models\Role::orderBy('name')->get() as $role)
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" 
                                                           id="edit_role_{{ $role->id }}" 
                                                           wire:model.live="roles" 
                                                           value="{{ $role->id }}">
                                                    <label class="form-check-label" for="edit_role_{{ $role->id }}">
                                                        {{ $role->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Entreprises</label>
                                    <div class="row">
                                        @foreach($companies as $company)
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" 
                                                           id="edit_company_{{ $company->id }}" 
                                                           wire:model.live="companies" 
                                                           value="{{ $company->id }}">
                                                    <label class="form-check-label" for="edit_company_{{ $company->id }}">
                                                        {{ $company->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeEditModal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal de suppression -->
    @if($showDeleteModal && $selectedUser)
        <div class="modal show d-block" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmer la suppression</h5>
                        <button type="button" class="btn-close" wire:click="closeDeleteModal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir supprimer l'utilisateur <strong>{{ $selectedUser->name }}</strong> ?</p>
                        <p class="text-muted">Cette action est réversible.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeDeleteModal">Annuler</button>
                        <button type="button" class="btn btn-danger" wire:click="deleteUser">Supprimer</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal de réinitialisation de mot de passe -->
    @if($showPasswordModal && $selectedUser)
        <div class="modal show d-block" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Réinitialiser le mot de passe</h5>
                        <button type="button" class="btn-close" wire:click="closePasswordModal"></button>
                    </div>
                    <form wire:submit.prevent="resetPassword">
                        <div class="modal-body">
                            <p class="mb-3">Utilisateur : <strong>{{ $selectedUser->name }}</strong></p>
                            <div class="mb-3">
                                <label for="reset_password" class="form-label">Nouveau mot de passe *</label>
                                <input type="password" class="form-control" id="reset_password" wire:model.live="password" required>
                                @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-3">
                                <label for="reset_password_confirmation" class="form-label">Confirmation du mot de passe *</label>
                                <input type="password" class="form-control" id="reset_password_confirmation" wire:model.live="password_confirmation" required>
                                @error('password_confirmation') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="send_password_email" wire:model.live="send_password_email">
                                <label class="form-check-label" for="send_password_email">
                                    Envoyer un email de notification à l'utilisateur
                                </label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closePasswordModal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Réinitialiser</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
.cursor-pointer {
    cursor: pointer;
}

.modal.show {
    display: block !important;
}

.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 12px;
    font-weight: bold;
}

.badge {
    font-size: 0.75em;
}
</style>
