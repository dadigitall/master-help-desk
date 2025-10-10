@extends('layouts.app')

@section('title', 'Gestion des Utilisateurs - Administration')

@section('content')
<div class="container-fluid">
    <livewire:admin.users.user-index />
</div>
@endsection

@push('styles')
<style>
    .admin-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
    }
    
    .admin-header h1 {
        margin: 0;
        font-weight: 600;
    }
    
    .admin-header .breadcrumb {
        background: transparent;
        padding: 0;
        margin: 0.5rem 0 0 0;
    }
    
    .admin-header .breadcrumb-item + .breadcrumb-item::before {
        color: rgba(255, 255, 255, 0.7);
        content: ">";
    }
    
    .admin-header .breadcrumb-item a {
        color: rgba(255, 255, 255, 0.9);
        text-decoration: none;
    }
    
    .admin-header .breadcrumb-item a:hover {
        color: white;
    }
    
    .admin-header .breadcrumb-item.active {
        color: rgba(255, 255, 255, 0.8);
    }
    
    .stats-card {
        transition: transform 0.2s ease-in-out;
    }
    
    .stats-card:hover {
        transform: translateY(-2px);
    }
    
    .avatar-sm {
        width: 32px;
        height: 32px;
        font-size: 12px;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }
    
    .table-actions .btn-group {
        display: flex;
        gap: 0.25rem;
    }
    
    .modal.show {
        display: block !important;
    }
    
    .cursor-pointer {
        cursor: pointer;
    }
    
    .badge {
        font-size: 0.75em;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    
    .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    
    .alert-info {
        background-color: #e7f3ff;
        border-color: #b8daff;
        color: #0c5460;
    }
    
    .table th {
        border-top: none;
        font-weight: 600;
        color: #495057;
        background-color: #f8f9fa;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    .pagination {
        margin-top: 1rem;
    }
    
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: 1px solid rgba(0, 0, 0, 0.125);
    }
    
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
        font-weight: 600;
    }
    
    .modal-content {
        border: none;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .modal-header {
        border-bottom: 1px solid #dee2e6;
    }
    
    .modal-footer {
        border-top: 1px solid #dee2e6;
    }
    
    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
    }
    
    .form-control:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    .btn-primary {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    
    .btn-primary:hover {
        background-color: #0b5ed7;
        border-color: #0a58ca;
    }
    
    .btn-outline-primary:hover {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    
    .btn-success {
        background-color: #198754;
        border-color: #198754;
    }
    
    .btn-warning {
        background-color: #ffc107;
        border-color: #ffc107;
        color: #000;
    }
    
    .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
    }
    
    .btn-outline-secondary {
        color: #6c757d;
        border-color: #6c757d;
    }
    
    .btn-outline-secondary:hover {
        background-color: #6c757d;
        border-color: #6c757d;
        color: white;
    }
</style>
@endpush

@push('scripts')
<script>
    // Gestion des notifications
    window.addEventListener('showNotification', function(event) {
        const notification = event.detail;
        
        // Créer l'élément de notification
        const notificationEl = document.createElement('div');
        notificationEl.className = `alert alert-${notification.type} alert-dismissible fade show position-fixed`;
        notificationEl.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notificationEl.innerHTML = `
            ${notification.message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notificationEl);
        
        // Auto-suppression après 5 secondes
        setTimeout(() => {
            if (notificationEl.parentNode) {
                notificationEl.parentNode.removeChild(notificationEl);
            }
        }, 5000);
    });
    
    // Gestion des modales
    document.addEventListener('DOMContentLoaded', function() {
        // Fermer les modales avec la touche Escape
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const modals = document.querySelectorAll('.modal.show');
                modals.forEach(modal => {
                    const closeBtn = modal.querySelector('.btn-close');
                    if (closeBtn) {
                        closeBtn.click();
                    }
                });
            }
        });
        
        // Gestion des tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
    
    // Animation des cartes de statistiques
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.stats-card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
    });
    
    // Gestion de la sélection multiple
    function updateBulkActionsVisibility() {
        const selectedCount = document.querySelectorAll('input[name="selectedUsers[]"]:checked').length;
        const bulkActions = document.querySelector('.bulk-actions');
        
        if (bulkActions) {
            if (selectedCount > 0) {
                bulkActions.style.display = 'block';
                bulkActions.style.opacity = '0';
                setTimeout(() => {
                    bulkActions.style.transition = 'opacity 0.3s ease';
                    bulkActions.style.opacity = '1';
                }, 10);
            } else {
                bulkActions.style.opacity = '0';
                setTimeout(() => {
                    bulkActions.style.display = 'none';
                }, 300);
            }
        }
    }
    
    // Observer les changements de sélection
    document.addEventListener('change', function(event) {
        if (event.target.matches('input[name="selectedUsers[]"]')) {
            updateBulkActionsVisibility();
        }
    });
    
    // Gestion du tri
    document.addEventListener('click', function(event) {
        if (event.target.matches('[wire\\:click*="sortBy"]')) {
            // Ajouter un effet de chargement
            event.target.style.opacity = '0.5';
            setTimeout(() => {
                event.target.style.opacity = '1';
            }, 500);
        }
    });
    
    // Gestion des filtres
    document.addEventListener('change', function(event) {
        if (event.target.matches('[wire\\:model*="search"], [wire\\:model*="role"], [wire\\:model*="status"], [wire\\:model*="company"]')) {
            // Ajouter un effet de chargement subtil
            const table = document.querySelector('.table-responsive');
            if (table) {
                table.style.opacity = '0.7';
                setTimeout(() => {
                    table.style.opacity = '1';
                }, 300);
            }
        }
    });
    
    // Validation des formulaires
    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function(event) {
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Chargement...';
                    
                    // Réactiver le bouton après 5 secondes (au cas où)
                    setTimeout(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = submitBtn.getAttribute('data-original-text') || 'Enregistrer';
                    }, 5000);
                }
            });
        });
        
        // Sauvegarder le texte original des boutons
        document.querySelectorAll('button[type="submit"]').forEach(btn => {
            btn.setAttribute('data-original-text', btn.textContent);
        });
    });
</script>
@endpush
