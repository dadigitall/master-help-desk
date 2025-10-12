<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Route;

class Breadcrumb extends Component
{
    public $items;
    public $currentPage;

    public function __construct()
    {
        $this->items = $this->generateBreadcrumb();
        $this->currentPage = $this->getCurrentPage();
    }

    private function generateBreadcrumb()
    {
        $breadcrumb = [];
        $routeName = Route::currentRouteName();
        $routeParameters = Route::current()->parameters();

        // Base breadcrumb mapping
        $breadcrumbMap = [
            'dashboard' => ['Dashboard', route('dashboard')],
            'tickets.index' => ['Tickets', route('tickets')],
            'tickets.create' => ['New Ticket', route('tickets.create')],
            'tickets.show' => ['Ticket Details', '#'],
            'projects.index' => ['Projects', route('projects')],
            'projects.create' => ['New Project', route('projects.create')],
            'projects.show' => ['Project Details', '#'],
            'companies.index' => ['Companies', route('companies')],
            'companies.create' => ['New Company', route('companies.create')],
            'companies.show' => ['Company Details', '#'],
            'users.index' => ['Users', '#'],
            'reports.index' => ['Reports', '#'],
            'settings.index' => ['Settings', '#'],
        ];

        // Generate breadcrumb based on route
        if (isset($breadcrumbMap[$routeName])) {
            $breadcrumb[] = ['label' => 'Home', 'url' => route('dashboard'), 'icon' => 'fas fa-home'];
            $breadcrumb[] = ['label' => $breadcrumbMap[$routeName][0], 'url' => $breadcrumbMap[$routeName][1]];
        } else {
            // Default breadcrumb
            $breadcrumb[] = ['label' => 'Home', 'url' => route('dashboard'), 'icon' => 'fas fa-home'];
        }

        return $breadcrumb;
    }

    private function getCurrentPage()
    {
        $routeName = Route::currentRouteName();
        $pageTitles = [
            'dashboard' => 'Dashboard Analytics',
            'tickets.index' => 'Tickets Management',
            'tickets.create' => 'Create New Ticket',
            'tickets.show' => 'Ticket Details',
            'projects.index' => 'Projects Overview',
            'projects.create' => 'Create New Project',
            'projects.show' => 'Project Details',
            'companies.index' => 'Companies Directory',
            'companies.create' => 'Add New Company',
            'companies.show' => 'Company Profile',
            'users.index' => 'User Management',
            'reports.index' => 'Reports & Analytics',
            'settings.index' => 'System Settings',
        ];

        return $pageTitles[$routeName] ?? 'Dashboard';
    }

    public function render()
    {
        return view('components.breadcrumb');
    }
}
