<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Constructeur avec middleware
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('permission:manage users');
        $this->middleware('permission:delete users')->only(['destroy', 'forceDelete']);
        $this->middleware('permission:restore users')->only(['restore']);
    }

    /**
     * Afficher la liste des utilisateurs avec pagination et recherche
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $query = User::with(['companies', 'roles', 'permissions'])
            ->withCount(['tickets', 'comments', 'companies']);

        // Recherche par nom ou email
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('id', 'LIKE', "%{$search}%");
            });
        }

        // Filtre par rôle
        if ($request->filled('role')) {
            $role = $request->get('role');
            $query->whereHas('roles', function (Builder $q) use ($role) {
                $q->where('name', $role);
            });
        }

        // Filtre par statut (actif/inactif)
        if ($request->filled('status')) {
            $status = $request->get('status');
            if ($status === 'active') {
                $query->whereNull('email_verified_at');
            } elseif ($status === 'verified') {
                $query->whereNotNull('email_verified_at');
            }
        }

        // Filtre par entreprise
        if ($request->filled('company')) {
            $company = $request->get('company');
            $query->whereHas('companies', function (Builder $q) use ($company) {
                $q->where('companies.id', $company);
            });
        }

        // Filtre par date de création
        if ($request->filled('date_from')) {
            $dateFrom = Carbon::parse($request->get('date_from'))->startOfDay();
            $query->where('created_at', '>=', $dateFrom);
        }

        if ($request->filled('date_to')) {
            $dateTo = Carbon::parse($request->get('date_to'))->endOfDay();
            $query->where('created_at', '<=', $dateTo);
        }

        // Tri
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if (in_array($sortBy, ['name', 'email', 'created_at', 'last_login_at', 'tickets_count'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $users = $query->paginate(15)->withQueryString();

        // Statistiques pour le dashboard
        $stats = [
            'total' => User::count(),
            'verified' => User::whereNotNull('email_verified_at')->count(),
            'active_today' => User::whereDate('last_login_at', today())->count(),
            'new_this_month' => User::whereMonth('created_at', now()->month)->count(),
        ];

        // Données pour les filtres
        $roles = Role::orderBy('name')->get();
        $companies = Company::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'stats', 'roles', 'companies'));
    }

    /**
     * Afficher le formulaire de création d'utilisateur
     */
    public function create()
    {
        $this->authorize('create', User::class);

        $roles = Role::orderBy('name')->get();
        $companies = Company::orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get();

        return view('admin.users.create', compact('roles', 'companies', 'permissions'));
    }

    /**
     * Stocker un nouvel utilisateur
     */
    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles' => ['array', 'exists:roles,id'],
            'permissions' => ['array', 'exists:permissions,id'],
            'companies' => ['array', 'exists:companies,id'],
            'is_active' => ['boolean'],
            'send_welcome_email' => ['boolean'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Créer l'utilisateur
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'email_verified_at' => $request->boolean('send_welcome_email') ? now() : null,
            ]);

            // Assigner les rôles
            if ($request->filled('roles')) {
                $user->roles()->sync($request->roles);
            }

            // Assigner les permissions directes
            if ($request->filled('permissions')) {
                $user->permissions()->sync($request->permissions);
            }

            // Associer aux entreprises
            if ($request->filled('companies')) {
                $user->companies()->sync($request->companies);
            }

            // Envoyer l'email de bienvenue
            if ($request->boolean('send_welcome_email')) {
                $user->sendWelcomeNotification();
            }

            DB::commit();

            Log::info('Utilisateur créé par administrateur', [
                'user_id' => $user->id,
                'email' => $user->email,
                'admin_id' => auth()->id(),
            ]);

            return redirect()
                ->route('admin.users.show', $user)
                ->with('success', 'Utilisateur créé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création de l\'utilisateur', [
                'error' => $e->getMessage(),
                'data' => $request->all(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Une erreur est survenue lors de la création de l\'utilisateur.')
                ->withInput();
        }
    }

    /**
     * Afficher les détails d'un utilisateur
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);

        $user->load([
            'roles',
            'permissions',
            'companies',
            'tickets' => function ($query) {
                $query->latest()->limit(10);
            },
            'comments' => function ($query) {
                $query->latest()->limit(10);
            },
            'activityLogs' => function ($query) {
                $query->latest()->limit(20);
            }
        ]);

        // Statistiques de l'utilisateur
        $stats = [
            'tickets_created' => $user->tickets()->count(),
            'tickets_assigned' => $user->assignedTickets()->count(),
            'comments_count' => $user->comments()->count(),
            'companies_count' => $user->companies()->count(),
            'last_login' => $user->last_login_at,
            'login_count' => $user->login_count ?? 0,
        ];

        // Activité récente
        $recentActivity = $user->activityLogs()
            ->with(['causer'])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.users.show', compact('user', 'stats', 'recentActivity'));
    }

    /**
     * Afficher le formulaire d'édition d'utilisateur
     */
    public function edit(User $user)
    {
        $this->authorize('update', $user);

        $roles = Role::orderBy('name')->get();
        $companies = Company::orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get();

        $userRoles = $user->roles->pluck('id')->toArray();
        $userPermissions = $user->permissions->pluck('id')->toArray();
        $userCompanies = $user->companies->pluck('id')->toArray();

        return view('admin.users.edit', compact(
            'user',
            'roles',
            'companies',
            'permissions',
            'userRoles',
            'userPermissions',
            'userCompanies'
        ));
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'roles' => ['array', 'exists:roles,id'],
            'permissions' => ['array', 'exists:permissions,id'],
            'companies' => ['array', 'exists:companies,id'],
            'is_active' => ['boolean'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Mettre à jour les informations de base
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            // Mettre à jour les rôles
            if ($request->has('roles')) {
                $user->roles()->sync($request->roles);
            }

            // Mettre à jour les permissions directes
            if ($request->has('permissions')) {
                $user->permissions()->sync($request->permissions);
            }

            // Mettre à jour les entreprises
            if ($request->has('companies')) {
                $user->companies()->sync($request->companies);
            }

            DB::commit();

            Log::info('Utilisateur mis à jour par administrateur', [
                'user_id' => $user->id,
                'email' => $user->email,
                'admin_id' => auth()->id(),
                'changes' => $request->all(),
            ]);

            return redirect()
                ->route('admin.users.show', $user)
                ->with('success', 'Utilisateur mis à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la mise à jour de l\'utilisateur', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'data' => $request->all(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Une erreur est survenue lors de la mise à jour de l\'utilisateur.')
                ->withInput();
        }
    }

    /**
     * Supprimer un utilisateur (soft delete)
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        // Empêcher la suppression de soi-même
        if ($user->id === auth()->id()) {
            return redirect()
                ->back()
                ->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        try {
            $userName = $user->name;
            $userEmail = $user->email;

            // Soft delete
            $user->delete();

            Log::warning('Utilisateur supprimé par administrateur', [
                'user_id' => $user->id,
                'email' => $userEmail,
                'name' => $userName,
                'admin_id' => auth()->id(),
            ]);

            return redirect()
                ->route('admin.users.index')
                ->with('success', "L'utilisateur {$userName} a été supprimé avec succès.");

        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de l\'utilisateur', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);

            return redirect()
                ->back()
                ->with('error', 'Une erreur est survenue lors de la suppression de l\'utilisateur.');
        }
    }

    /**
     * Restaurer un utilisateur supprimé
     */
    public function restore($id)
    {
        $this->authorize('restore', User::class);

        $user = User::withTrashed()->findOrFail($id);

        try {
            $user->restore();

            Log::info('Utilisateur restauré par administrateur', [
                'user_id' => $user->id,
                'email' => $user->email,
                'admin_id' => auth()->id(),
            ]);

            return redirect()
                ->route('admin.users.show', $user)
                ->with('success', 'Utilisateur restauré avec succès.');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la restauration de l\'utilisateur', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);

            return redirect()
                ->back()
                ->with('error', 'Une erreur est survenue lors de la restauration de l\'utilisateur.');
        }
    }

    /**
     * Supprimer définitivement un utilisateur
     */
    public function forceDelete($id)
    {
        $this->authorize('forceDelete', User::class);

        $user = User::withTrashed()->findOrFail($id);

        // Empêcher la suppression définitive de soi-même
        if ($user->id === auth()->id()) {
            return redirect()
                ->back()
                ->with('error', 'Vous ne pouvez pas supprimer définitivement votre propre compte.');
        }

        try {
            $userName = $user->name;
            $userEmail = $user->email;

            // Supprimer définitivement
            $user->forceDelete();

            Log::critical('Utilisateur supprimé définitivement par administrateur', [
                'user_id' => $user->id,
                'email' => $userEmail,
                'name' => $userName,
                'admin_id' => auth()->id(),
            ]);

            return redirect()
                ->route('admin.users.index')
                ->with('success', "L'utilisateur {$userName} a été supprimé définitivement.");

        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression définitive de l\'utilisateur', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);

            return redirect()
                ->back()
                ->with('error', 'Une erreur est survenue lors de la suppression définitive de l\'utilisateur.');
        }
    }

    /**
     * Réinitialiser le mot de passe d'un utilisateur
     */
    public function resetPassword(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $validator = Validator::make($request->all(), [
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'send_email' => ['boolean'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            // Envoyer l'email de notification
            if ($request->boolean('send_email')) {
                $user->sendPasswordResetNotification();
            }

            Log::info('Mot de passe réinitialisé par administrateur', [
                'user_id' => $user->id,
                'email' => $user->email,
                'admin_id' => auth()->id(),
            ]);

            return redirect()
                ->route('admin.users.show', $user)
                ->with('success', 'Mot de passe réinitialisé avec succès.');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la réinitialisation du mot de passe', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);

            return redirect()
                ->back()
                ->with('error', 'Une erreur est survenue lors de la réinitialisation du mot de passe.');
        }
    }

    /**
     * Activer/Désactiver un utilisateur
     */
    public function toggleStatus(User $user)
    {
        $this->authorize('update', $user);

        // Empêcher la désactivation de soi-même
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez pas désactiver votre propre compte.'
            ], 403);
        }

        try {
            $user->update([
                'email_verified_at' => $user->email_verified_at ? null : now(),
            ]);

            $status = $user->email_verified_at ? 'activé' : 'désactivé';

            Log::info('Statut utilisateur modifié par administrateur', [
                'user_id' => $user->id,
                'email' => $user->email,
                'status' => $status,
                'admin_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => "Utilisateur {$status} avec succès.",
                'status' => $user->email_verified_at ? 'active' : 'inactive'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la modification du statut utilisateur', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la modification du statut.'
            ], 500);
        }
    }

    /**
     * Exporter la liste des utilisateurs
     */
    public function export(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $query = User::with(['roles', 'companies']);

        // Appliquer les mêmes filtres que la méthode index
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $role = $request->get('role');
            $query->whereHas('roles', function (Builder $q) use ($role) {
                $q->where('name', $role);
            });
        }

        $users = $query->get();

        // Préparer les données pour l'export
        $exportData = $users->map(function ($user) {
            return [
                'ID' => $user->id,
                'Nom' => $user->name,
                'Email' => $user->email,
                'Rôles' => $user->roles->pluck('name')->implode(', '),
                'Entreprises' => $user->companies->pluck('name')->implode(', '),
                'Vérifié' => $user->email_verified_at ? 'Oui' : 'Non',
                'Dernière connexion' => $user->last_login_at?->format('d/m/Y H:i'),
                'Créé le' => $user->created_at->format('d/m/Y H:i'),
            ];
        });

        // Générer le CSV
        $filename = 'utilisateurs_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($exportData) {
            $file = fopen('php://output', 'w');
            
            // Ajouter l'en-tête BOM pour Excel
            fwrite($file, "\xEF\xBB\xBF");
            
            // En-tête CSV
            fputcsv($file, array_keys($exportData->first()));
            
            // Données
            foreach ($exportData as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
