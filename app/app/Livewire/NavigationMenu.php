<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class NavigationMenu extends Component
{
    public $user;
    public $notifications = [];
    public $showUserMenu = false;
    public $showNotifications = false;
    public $sidebarOpen = true;
    public $sidebarCollapsed = false;

    protected $listeners = ['refreshNavigation' => '$refresh'];

    public function mount()
    {
        $this->user = Auth::user();
        $this->loadNotifications();
        $this->loadSidebarState();
    }

    public function loadSidebarState()
    {
        // Charger l'état de la sidebar depuis la session
        $this->sidebarOpen = Session::get('sidebar_open', true);
        $this->sidebarCollapsed = Session::get('sidebar_collapsed', false);
    }

    public function saveSidebarState()
    {
        // Sauvegarder l'état de la sidebar dans la session
        Session::put('sidebar_open', $this->sidebarOpen);
        Session::put('sidebar_collapsed', $this->sidebarCollapsed);
        
        // Émettre un événement pour synchroniser avec le frontend
        $this->dispatch('sidebarStateChanged', [
            'open' => $this->sidebarOpen,
            'collapsed' => $this->sidebarCollapsed
        ]);
    }

    public function loadNotifications()
    {
        // Charger les notifications récentes
        $this->notifications = [
            ['id' => 1, 'message' => 'Nouveau ticket créé', 'time' => 'Il y a 2 min', 'read' => false],
            ['id' => 2, 'message' => 'Ticket #1234 résolu', 'time' => 'Il y a 15 min', 'read' => false],
            ['id' => 3, 'message' => 'Mise à jour du projet', 'time' => 'Il y a 1 heure', 'read' => true],
        ];
    }

    public function toggleSidebar()
    {
        $this->sidebarOpen = !$this->sidebarOpen;
        if ($this->sidebarOpen) {
            $this->sidebarCollapsed = false;
        }
        $this->saveSidebarState();
    }

    public function toggleSidebarCollapse()
    {
        $this->sidebarCollapsed = !$this->sidebarCollapsed;
        $this->saveSidebarState();
    }

    public function expandSidebar()
    {
        $this->sidebarOpen = true;
        $this->sidebarCollapsed = false;
        $this->saveSidebarState();
    }

    public function collapseSidebar()
    {
        $this->sidebarCollapsed = true;
        $this->saveSidebarState();
    }

    public function toggleUserMenu()
    {
        $this->showUserMenu = !$this->showUserMenu;
        $this->showNotifications = false;
    }

    public function toggleNotifications()
    {
        $this->showNotifications = !$this->showNotifications;
        $this->showUserMenu = false;
    }

    public function markAsRead($notificationId)
    {
        // Marquer la notification comme lue
        foreach ($this->notifications as &$notification) {
            if ($notification['id'] === $notificationId) {
                $notification['read'] = true;
                break;
            }
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }

    public function render()
    {
        return view('livewire.navigation-menu');
    }
}
