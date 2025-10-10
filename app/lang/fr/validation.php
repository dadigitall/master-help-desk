<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'Le champ :attribute doit être accepté.',
    'accepted_if' => 'Le champ :attribute doit être accepté lorsque :other est :value.',
    'active_url' => 'Le champ :attribute n\'est pas une URL valide.',
    'after' => 'Le champ :attribute doit être une date postérieure au :date.',
    'after_or_equal' => 'Le champ :attribute doit être une date postérieure ou égale au :date.',
    'alpha' => 'Le champ :attribute doit contenir uniquement des lettres.',
    'alpha_dash' => 'Le champ :attribute doit contenir uniquement des lettres, des chiffres et des tirets.',
    'alpha_num' => 'Le champ :attribute doit contenir uniquement des lettres et des chiffres.',
    'array' => 'Le champ :attribute doit être un tableau.',
    'ascii' => 'Le champ :attribute doit contenir uniquement des caractères alphanumériques et des symboles.',
    'before' => 'Le champ :attribute doit être une date antérieure au :date.',
    'before_or_equal' => 'Le champ :attribute doit être une date antérieure ou égale au :date.',
    'between' => [
        'array' => 'Le champ :attribute doit contenir entre :min et :max éléments.',
        'file' => 'Le champ :attribute doit peser entre :min et :max kiloctets.',
        'numeric' => 'Le champ :attribute doit être compris entre :min et :max.',
        'string' => 'Le champ :attribute doit contenir entre :min et :max caractères.',
    ],
    'boolean' => 'Le champ :attribute doit être vrai ou faux.',
    'can' => 'Le champ :attribute contient une valeur non autorisée.',
    'confirmed' => 'Le champ de confirmation :attribute ne correspond pas.',
    'current_password' => 'Le mot de passe est incorrect.',
    'date' => 'Le champ :attribute n\'est pas une date valide.',
    'date_equals' => 'Le champ :attribute doit être une date égale à :date.',
    'date_format' => 'Le champ :attribute ne correspond pas au format :format.',
    'decimal' => 'Le champ :attribute doit avoir :decimal décimales.',
    'declined' => 'Le champ :attribute doit être décliné.',
    'declined_if' => 'Le champ :attribute doit être décliné lorsque :other est :value.',
    'different' => 'Les champs :attribute et :other doivent être différents.',
    'digits' => 'Le champ :attribute doit contenir :digits chiffres.',
    'digits_between' => 'Le champ :attribute doit contenir entre :min et :max chiffres.',
    'dimensions' => 'Le champ :attribute a des dimensions d\'image invalides.',
    'distinct' => 'Le champ :attribute a une valeur en double.',
    'doesnt_end_with' => 'Le champ :attribute ne peut pas se terminer par l\'une des valeurs suivantes : :values.',
    'doesnt_start_with' => 'Le champ :attribute ne peut pas commencer par l\'une des valeurs suivantes : :values.',
    'email' => 'Le champ :attribute doit être une adresse email valide.',
    'ends_with' => 'Le champ :attribute doit se terminer par l\'une des valeurs suivantes : :values.',
    'enum' => 'Le :attribute sélectionné est invalide.',
    'exists' => 'Le :attribute sélectionné est invalide.',
    'file' => 'Le champ :attribute doit être un fichier.',
    'filled' => 'Le champ :attribute doit avoir une valeur.',
    'gt' => [
        'array' => 'Le champ :attribute doit contenir plus de :value éléments.',
        'file' => 'Le champ :attribute doit peser plus de :value kiloctets.',
        'numeric' => 'Le champ :attribute doit être supérieur à :value.',
        'string' => 'Le champ :attribute doit contenir plus de :value caractères.',
    ],
    'gte' => [
        'array' => 'Le champ :attribute doit contenir au moins :value éléments.',
        'file' => 'Le champ :attribute doit peser au moins :value kiloctets.',
        'numeric' => 'Le champ :attribute doit être supérieur ou égal à :value.',
        'string' => 'Le champ :attribute doit contenir au moins :value caractères.',
    ],
    'image' => 'Le champ :attribute doit être une image.',
    'in' => 'Le :attribute sélectionné est invalide.',
    'in_array' => 'Le champ :attribute n\'existe pas dans :other.',
    'integer' => 'Le champ :attribute doit être un entier.',
    'ip' => 'Le champ :attribute doit être une adresse IP valide.',
    'ipv4' => 'Le champ :attribute doit être une adresse IPv4 valide.',
    'ipv6' => 'Le champ :attribute doit être une adresse IPv6 valide.',
    'json' => 'Le champ :attribute doit être une chaîne JSON valide.',
    'lowercase' => 'Le champ :attribute doit être en minuscules.',
    'lt' => [
        'array' => 'Le champ :attribute doit contenir moins de :value éléments.',
        'file' => 'Le champ :attribute doit peser moins de :value kiloctets.',
        'numeric' => 'Le champ :attribute doit être inférieur à :value.',
        'string' => 'Le champ :attribute doit contenir moins de :value caractères.',
    ],
    'lte' => [
        'array' => 'Le champ :attribute doit contenir au plus :value éléments.',
        'file' => 'Le champ :attribute doit peser au plus :value kiloctets.',
        'numeric' => 'Le champ :attribute doit être inférieur ou égal à :value.',
        'string' => 'Le champ :attribute doit contenir au plus :value caractères.',
    ],
    'mac_address' => 'Le champ :attribute doit être une adresse MAC valide.',
    'max' => [
        'array' => 'Le champ :attribute ne peut pas contenir plus de :max éléments.',
        'file' => 'Le champ :attribute ne peut pas peser plus de :max kiloctets.',
        'numeric' => 'Le champ :attribute ne peut pas être supérieur à :max.',
        'string' => 'Le champ :attribute ne peut pas contenir plus de :max caractères.',
    ],
    'max_digits' => 'Le champ :attribute ne peut pas avoir plus de :max chiffres.',
    'mimes' => 'Le champ :attribute doit être un fichier de type : :values.',
    'mimetypes' => 'Le champ :attribute doit être un fichier de type : :values.',
    'min' => [
        'array' => 'Le champ :attribute doit contenir au moins :min éléments.',
        'file' => 'Le champ :attribute doit peser au moins :min kiloctets.',
        'numeric' => 'Le champ :attribute doit être au moins :min.',
        'string' => 'Le champ :attribute doit contenir au moins :min caractères.',
    ],
    'min_digits' => 'Le champ :attribute doit avoir au moins :min chiffres.',
    'missing' => 'Le champ :attribute doit être manquant.',
    'missing_if' => 'Le champ :attribute doit être manquant lorsque :other est :value.',
    'missing_unless' => 'Le champ :attribute doit être manquant à moins que :other ne soit :value.',
    'missing_with' => 'Le champ :attribute doit être manquant lorsque :values est présent.',
    'missing_with_all' => 'Le champ :attribute doit être manquant lorsque :values sont présents.',
    'multiple_of' => 'Le champ :attribute doit être un multiple de :value.',
    'not_in' => 'Le :attribute sélectionné est invalide.',
    'not_regex' => 'Le format du champ :attribute est invalide.',
    'numeric' => 'Le champ :attribute doit être un nombre.',
    'password' => 'Le mot de passe est incorrect.',
    'present' => 'Le champ :attribute doit être présent.',
    'prohibited' => 'Le champ :attribute est interdit.',
    'prohibited_if' => 'Le champ :attribute est interdit lorsque :other est :value.',
    'prohibited_unless' => 'Le champ :attribute est interdit à moins que :other ne soit dans :values.',
    'prohibits' => 'Le champ :attribute interdit :other d\'être présent.',
    'regex' => 'Le format du champ :attribute est invalide.',
    'required' => 'Le champ :attribute est obligatoire.',
    'required_array_keys' => 'Le champ :attribute doit contenir les entrées : :values.',
    'required_if' => 'Le champ :attribute est obligatoire lorsque :other est :value.',
    'required_if_accepted' => 'Le champ :attribute est obligatoire lorsque :other est accepté.',
    'required_unless' => 'Le champ :attribute est obligatoire sauf si :other est dans :values.',
    'required_with' => 'Le champ :attribute est obligatoire lorsque :values est présent.',
    'required_with_all' => 'Le champ :attribute est obligatoire lorsque :values sont présents.',
    'required_without' => 'Le champ :attribute est obligatoire lorsque :values n\'est pas présent.',
    'required_without_all' => 'Le champ :attribute est obligatoire lorsqu\'aucun des :values n\'est présent.',
    'same' => 'Les champs :attribute et :other doivent correspondre.',
    'size' => [
        'array' => 'Le champ :attribute doit contenir :size éléments.',
        'file' => 'Le champ :attribute doit peser :size kiloctets.',
        'numeric' => 'Le champ :attribute doit être :size.',
        'string' => 'Le champ :attribute doit contenir :size caractères.',
    ],
    'starts_with' => 'Le champ :attribute doit commencer par l\'une des valeurs suivantes : :values.',
    'string' => 'Le champ :attribute doit être une chaîne de caractères.',
    'timezone' => 'Le champ :attribute doit être un fuseau horaire valide.',
    'unique' => 'Le :attribute a déjà été pris.',
    'uploaded' => 'Le fichier :attribute n\'a pas pu être téléchargé.',
    'uppercase' => 'Le champ :attribute doit être en majuscules.',
    'url' => 'Le champ :attribute doit être une URL valide.',
    'ulid' => 'Le champ :attribute doit être un ULID valide.',
    'uuid' => 'Le champ :attribute doit être un UUID valide.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "rule.attribute" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
        
        // Validation personnalisée pour les tickets
        'title' => [
            'required' => 'Le titre du ticket est obligatoire.',
            'min' => 'Le titre du ticket doit contenir au moins :min caractères.',
            'max' => 'Le titre du ticket ne peut pas dépasser :max caractères.',
        ],
        'description' => [
            'required' => 'La description du ticket est obligatoire.',
            'min' => 'La description doit contenir au moins :min caractères.',
        ],
        'priority' => [
            'required' => 'La priorité du ticket est obligatoire.',
            'in' => 'La priorité sélectionnée est invalide.',
        ],
        'status' => [
            'required' => 'Le statut du ticket est obligatoire.',
            'in' => 'Le statut sélectionné est invalide.',
        ],
        
        // Validation personnalisée pour les projets
        'project_name' => [
            'required' => 'Le nom du projet est obligatoire.',
            'unique' => 'Ce nom de projet existe déjà.',
        ],
        'project_description' => [
            'required' => 'La description du projet est obligatoire.',
        ],
        'start_date' => [
            'required' => 'La date de début est obligatoire.',
            'date' => 'La date de début n\'est pas valide.',
        ],
        'end_date' => [
            'required' => 'La date de fin est obligatoire.',
            'date' => 'La date de fin n\'est pas valide.',
            'after' => 'La date de fin doit être postérieure à la date de début.',
        ],
        
        // Validation personnalisée pour les entreprises
        'company_name' => [
            'required' => 'Le nom de l\'entreprise est obligatoire.',
            'unique' => 'Ce nom d\'entreprise existe déjà.',
        ],
        'company_email' => [
            'required' => 'L\'email de l\'entreprise est obligatoire.',
            'email' => 'L\'email de l\'entreprise n\'est pas valide.',
            'unique' => 'Cet email d\'entreprise existe déjà.',
        ],
        'company_phone' => [
            'required' => 'Le téléphone de l\'entreprise est obligatoire.',
            'regex' => 'Le numéro de téléphone n\'est pas valide.',
        ],
        
        // Validation personnalisée pour les utilisateurs
        'name' => [
            'required' => 'Le nom est obligatoire.',
            'min' => 'Le nom doit contenir au moins :min caractères.',
            'max' => 'Le nom ne peut pas dépasser :max caractères.',
        ],
        'email' => [
            'required' => 'L\'email est obligatoire.',
            'email' => 'L\'email n\'est pas valide.',
            'unique' => 'Cet email existe déjà.',
        ],
        'password' => [
            'required' => 'Le mot de passe est obligatoire.',
            'min' => 'Le mot de passe doit contenir au moins :min caractères.',
            'confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ],
        'role' => [
            'required' => 'Le rôle est obligatoire.',
            'exists' => 'Le rôle sélectionné n\'existe pas.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'name' => 'nom',
        'email' => 'adresse email',
        'password' => 'mot de passe',
        'password_confirmation' => 'confirmation du mot de passe',
        'title' => 'titre',
        'description' => 'description',
        'content' => 'contenu',
        'status' => 'statut',
        'priority' => 'priorité',
        'type' => 'type',
        'category' => 'catégorie',
        'company_name' => 'nom de l\'entreprise',
        'company_email' => 'email de l\'entreprise',
        'company_phone' => 'téléphone de l\'entreprise',
        'project_name' => 'nom du projet',
        'project_description' => 'description du projet',
        'start_date' => 'date de début',
        'end_date' => 'date de fin',
        'budget' => 'budget',
        'address' => 'adresse',
        'phone' => 'téléphone',
        'website' => 'site web',
        'role' => 'rôle',
        'permissions' => 'permissions',
        'avatar' => 'avatar',
        'file' => 'fichier',
        'attachment' => 'pièce jointe',
        'comment' => 'commentaire',
        'note' => 'note',
        'reason' => 'raison',
        'subject' => 'sujet',
        'message' => 'message',
        'url' => 'URL',
        'link' => 'lien',
        'tags' => 'étiquettes',
        'color' => 'couleur',
        'icon' => 'icône',
        'logo' => 'logo',
        'banner' => 'bannière',
        'thumbnail' => 'miniature',
        'image' => 'image',
        'video' => 'vidéo',
        'audio' => 'audio',
        'document' => 'document',
        'pdf' => 'PDF',
        'excel' => 'fichier Excel',
        'word' => 'fichier Word',
        'powerpoint' => 'fichier PowerPoint',
        'zip' => 'archive ZIP',
        'size' => 'taille',
        'weight' => 'poids',
        'height' => 'hauteur',
        'width' => 'largeur',
        'length' => 'longueur',
        'quantity' => 'quantité',
        'price' => 'prix',
        'cost' => 'coût',
        'total' => 'total',
        'amount' => 'montant',
        'currency' => 'devise',
        'discount' => 'remise',
        'tax' => 'taxe',
        'shipping' => 'livraison',
        'payment' => 'paiement',
        'invoice' => 'facture',
        'receipt' => 'reçu',
        'order' => 'commande',
        'purchase' => 'achat',
        'sale' => 'vente',
        'product' => 'produit',
        'service' => 'service',
        'subscription' => 'abonnement',
        'membership' => 'adhésion',
        'account' => 'compte',
        'profile' => 'profil',
        'settings' => 'paramètres',
        'preferences' => 'préférences',
        'notifications' => 'notifications',
        'alerts' => 'alertes',
        'reminders' => 'rappels',
        'events' => 'événements',
        'appointments' => 'rendez-vous',
        'meetings' => 'réunions',
        'tasks' => 'tâches',
        'notes' => 'notes',
        'logs' => 'journaux',
        'reports' => 'rapports',
        'statistics' => 'statistiques',
        'analytics' => 'analytique',
        'metrics' => 'métriques',
        'data' => 'données',
        'information' => 'information',
        'details' => 'détails',
        'summary' => 'résumé',
        'overview' => 'aperçu',
        'introduction' => 'introduction',
        'conclusion' => 'conclusion',
        'terms' => 'termes',
        'conditions' => 'conditions',
        'policy' => 'politique',
        'agreement' => 'accord',
        'contract' => 'contrat',
        'signature' => 'signature',
        'date' => 'date',
        'time' => 'heure',
        'datetime' => 'date et heure',
        'created_at' => 'date de création',
        'updated_at' => 'date de mise à jour',
        'deleted_at' => 'date de suppression',
        'expires_at' => 'date d\'expiration',
        'published_at' => 'date de publication',
        'archived_at' => 'date d\'archivage',
    ],
];
