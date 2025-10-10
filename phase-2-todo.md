# Phase 2: Modèles de Données et Migrations - TODO LIST

## Étape 2.1: Création des modèles principaux
- [ ] Créer le modèle Company avec champs personnalisés
- [ ] Créer le modèle Project avec champs personnalisés
- [ ] Créer le modèle Ticket avec champs personnalisés
- [ ] Créer le modèle Comment avec champs personnalisés
- [ ] Créer le modèle Chat avec champs personnalisés

## Étape 2.2: Création des modèles de configuration
- [ ] Créer le modèle TicketStatus
- [ ] Créer le modèle TicketPriority
- [ ] Créer le modèle TicketType
- [ ] Créer le modèle Icon
- [ ] Créer le modèle FavoriteProject

## Étape 2.3: Définition des relations entre modèles
- [ ] Définir les relations User-Company (Many-to-Many)
- [ ] Définir les relations User-Project (Many-to-Many)
- [ ] Définir les relations User-Ticket (Many-to-Many)
- [ ] Définir les relations Project-Ticket (One-to-Many)
- [ ] Définir les relations Ticket-Comment (One-to-Many)
- [ ] Définir les relations Ticket-Chat (One-to-Many)
- [ ] Définir les relations User-Comment (One-to-Many)
- [ ] Définir les relations User-Chat (One-to-Many)

## Étape 2.4: Création des migrations
- [ ] Créer la migration companies_table
- [ ] Créer la migration projects_table
- [ ] Créer la migration tickets_table
- [ ] Créer la migration comments_table
- [ ] Créer la migration chats_table
- [ ] Créer la migration ticket_statuses_table
- [ ] Créer la migration ticket_priorities_table
- [ ] Créer la migration ticket_types_table
- [ ] Créer la migration icons_table
- [ ] Créer la migration favorite_projects_table
- [ ] Créer les tables pivot (company_user, project_user, ticket_user)
- [ ] Ajouter les index nécessaires pour la performance
- [ ] Configurer les soft deletes sur les modèles appropriés
- [ ] Ajouter les contraintes de clés étrangères

## Étape 2.5: Seeders de base
- [ ] Créer le seeder pour les statuts de tickets
- [ ] Créer le seeder pour les priorités de tickets
- [ ] Créer le seeder pour les types de tickets
- [ ] Créer le seeder pour les icônes
- [ ] Mettre à jour le DatabaseSeeder principal
- [ ] Tester l'exécution des seeders

## Étape 2.6: Validation et tests
- [ ] Exécuter toutes les migrations
- [ ] Vérifier la structure des tables en base de données
- [ ] Tester les relations entre modèles avec Tinker
- [ ] Valider les seeders
- [ ] Documenter la structure des modèles

---

## Structure attendue après cette phase

```
app/
├── app/
│   ├── Models/
│   │   ├── User.php (existant)
│   │   ├── Company.php (nouveau)
│   │   ├── Project.php (nouveau)
│   │   ├── Ticket.php (nouveau)
│   │   ├── Comment.php (nouveau)
│   │   ├── Chat.php (nouveau)
│   │   ├── TicketStatus.php (nouveau)
│   │   ├── TicketPriority.php (nouveau)
│   │   ├── TicketType.php (nouveau)
│   │   ├── Icon.php (nouveau)
│   │   └── FavoriteProject.php (nouveau)
│   └── ...
├── database/
│   ├── migrations/ (nouvelles migrations)
│   └── seeders/ (nouveaux seeders)
└── ...
```

---

## Champs prévus pour chaque modèle

### Company
- id, name, description, email, phone, address, logo, active, created_at, updated_at, deleted_at

### Project  
- id, company_id, name, description, prefix, color, icon_id, active, created_by, created_at, updated_at, deleted_at

### Ticket
- id, project_id, ticket_number, title, description, status_id, priority_id, type_id, assigned_to, created_by, company_id, created_at, updated_at, deleted_at

### Comment
- id, ticket_id, user_id, content, is_internal, created_at, updated_at, deleted_at

### Chat
- id, ticket_id, user_id, message, is_customer, created_at, updated_at, deleted_at

### TicketStatus
- id, name, color, order, is_default, is_closed, created_at, updated_at

### TicketPriority  
- id, name, color, order, created_at, updated_at

### TicketType
- id, name, description, icon_id, color, created_at, updated_at

### Icon
- id, name, svg_content, category, created_at, updated_at

### FavoriteProject
- id, user_id, project_id, created_at, updated_at
