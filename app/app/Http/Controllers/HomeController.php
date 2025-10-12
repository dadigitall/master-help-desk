<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Project;
use App\Models\Company;
use App\Models\User;

class HomeController extends Controller
{
    /**
     * Affiche la page d'accueil
     */
    public function index()
    {
        // Statistiques pour la page d'accueil
        $stats = [
            'tickets_count' => Ticket::count(),
            'projects_count' => Project::count(),
            'companies_count' => Company::count(),
            'users_count' => User::count(),
            'open_tickets' => Ticket::where('status_id', 1)->count(), // Supposons que 1 = "Ouvert"
            'resolved_tickets' => Ticket::where('status_id', 3)->count(), // Supposons que 3 = "Résolu"
        ];

        // Tickets récents
        $recentTickets = Ticket::with(['company', 'project', 'status'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Projets récents
        $recentProjects = Project::with('company')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        return view('home', compact('stats', 'recentTickets', 'recentProjects'));
    }
}
