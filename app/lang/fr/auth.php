<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'Ces identifiants ne correspondent pas à nos enregistrements.',
    'password' => 'Le mot de passe fourni est incorrect.',
    'throttle' => 'Trop de tentatives de connexion. Veuillez réessayer dans :seconds secondes.',

    /*
    |--------------------------------------------------------------------------
    | Login Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during the login process.
    |
    */

    'login' => [
        'title' => 'Connexion',
        'heading' => 'Connectez-vous à votre compte',
        'subheading' => 'Bienvenue ! Connectez-vous pour continuer.',
        'email_label' => 'Adresse email',
        'email_placeholder' => 'Entrez votre adresse email',
        'password_label' => 'Mot de passe',
        'password_placeholder' => 'Entrez votre mot de passe',
        'remember_me' => 'Se souvenir de moi',
        'forgot_password' => 'Mot de passe oublié ?',
        'login_button' => 'Se connecter',
        'no_account' => 'Pas encore de compte ?',
        'register_here' => 'S\'inscrire ici',
        'success_message' => 'Connexion réussie !',
        'error_message' => 'Échec de la connexion. Veuillez vérifier vos identifiants.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Registration Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during the registration process.
    |
    */

    'register' => [
        'title' => 'Inscription',
        'heading' => 'Créez votre compte',
        'subheading' => 'Rejoignez-nous dès aujourd\'hui !',
        'name_label' => 'Nom complet',
        'name_placeholder' => 'Entrez votre nom complet',
        'email_label' => 'Adresse email',
        'email_placeholder' => 'Entrez votre adresse email',
        'password_label' => 'Mot de passe',
        'password_placeholder' => 'Entrez votre mot de passe',
        'password_confirmation_label' => 'Confirmer le mot de passe',
        'password_confirmation_placeholder' => 'Confirmez votre mot de passe',
        'terms_label' => 'J\'accepte les :terms',
        'register_button' => 'S\'inscrire',
        'has_account' => 'Vous avez déjà un compte ?',
        'login_here' => 'Connectez-vous ici',
        'success_message' => 'Inscription réussie ! Vérifiez votre email.',
        'error_message' => 'Échec de l\'inscription. Veuillez corriger les erreurs.',
        'terms' => 'conditions d\'utilisation',
        'privacy' => 'politique de confidentialité',
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Reset Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during the password reset process.
    |
    */

    'password_reset' => [
        'title' => 'Réinitialisation du mot de passe',
        'heading' => 'Réinitialiser votre mot de passe',
        'subheading' => 'Entrez votre adresse email pour recevoir un lien de réinitialisation.',
        'email_label' => 'Adresse email',
        'email_placeholder' => 'Entrez votre adresse email',
        'send_button' => 'Envoyer le lien de réinitialisation',
        'back_to_login' => 'Retour à la connexion',
        'success_message' => 'Un lien de réinitialisation a été envoyé à votre adresse email.',
        'error_message' => 'Échec de l\'envoi du lien. Veuillez vérifier votre adresse email.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Reset Form Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used on the password reset form.
    |
    */

    'reset' => [
        'title' => 'Définir un nouveau mot de passe',
        'heading' => 'Définir votre nouveau mot de passe',
        'subheading' => 'Choisissez un mot de passe sécurisé pour votre compte.',
        'email_label' => 'Adresse email',
        'password_label' => 'Nouveau mot de passe',
        'password_placeholder' => 'Entrez votre nouveau mot de passe',
        'password_confirmation_label' => 'Confirmer le mot de passe',
        'password_confirmation_placeholder' => 'Confirmez votre nouveau mot de passe',
        'reset_button' => 'Réinitialiser le mot de passe',
        'success_message' => 'Votre mot de passe a été réinitialisé avec succès !',
        'error_message' => 'Échec de la réinitialisation. Veuillez réessayer.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Verification Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during email verification.
    |
    */

    'verification' => [
        'title' => 'Vérification de l\'email',
        'heading' => 'Vérifiez votre adresse email',
        'subheading' => 'Nous avons envoyé un lien de vérification à votre adresse email.',
        'resend_button' => 'Renvoyer l\'email de vérification',
        'logout_button' => 'Se déconnecter',
        'success_message' => 'Votre adresse email a été vérifiée avec succès !',
        'error_message' => 'La vérification a échoué. Veuillez réessayer.',
        'already_verified' => 'Votre adresse email est déjà vérifiée.',
        'verification_sent' => 'Un nouvel email de vérification a été envoyé.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Two Factor Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during two factor authentication.
    |
    */

    'two_factor' => [
        'title' => 'Authentification à deux facteurs',
        'heading' => 'Vérification à deux facteurs',
        'subheading' => 'Entrez le code à 6 chiffres généré par votre application d\'authentification.',
        'code_label' => 'Code de vérification',
        'code_placeholder' => 'Entrez le code à 6 chiffres',
        'verify_button' => 'Vérifier',
        'recovery_code_title' => 'Utiliser un code de récupération',
        'recovery_code_label' => 'Code de récupération',
        'recovery_code_placeholder' => 'Entrez un code de récupération',
        'use_recovery_code' => 'Utiliser un code de récupération',
        'use_authenticator' => 'Utiliser l\'application d\'authentification',
        'success_message' => 'Authentification réussie !',
        'invalid_code' => 'Le code de vérification est invalide.',
        'invalid_recovery_code' => 'Le code de récupération est invalide.',
        'expired_code' => 'Le code de vérification a expiré.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Two Factor Challenge Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used on the two factor challenge page.
    |
    */

    'two_factor_challenge' => [
        'title' => 'Vérification à deux facteurs requise',
        'heading' => 'Vérification à deux facteurs',
        'subheading' => 'Votre compte est protégé par l\'authentification à deux facteurs.',
        'code_label' => 'Code de vérification',
        'code_placeholder' => 'Entrez le code à 6 chiffres',
        'verify_button' => 'Vérifier',
        'recovery_code_title' => 'Codes de récupération',
        'recovery_code_description' => 'Si vous n\'avez pas accès à votre application d\'authentification, vous pouvez utiliser l\'un de vos codes de récupération.',
        'recovery_code_label' => 'Code de récupération',
        'recovery_code_placeholder' => 'Entrez un code de récupération',
        'use_recovery_code' => 'Utiliser un code de récupération',
        'cancel_button' => 'Annuler',
        'success_message' => 'Vérification réussie !',
        'invalid_code' => 'Le code de vérification est invalide.',
        'too_many_attempts' => 'Trop de tentatives. Veuillez réessayer plus tard.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Two Factor Setup Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during two factor setup.
    |
    */

    'two_factor_setup' => [
        'title' => 'Configuration de l\'authentification à deux facteurs',
        'heading' => 'Sécurisez votre compte',
        'subheading' => 'Activez l\'authentification à deux facteurs pour une sécurité supplémentaire.',
        'step_1_title' => 'Étape 1 : Installez une application d\'authentification',
        'step_1_description' => 'Installez une application d\'authentification comme Google Authenticator, Authy ou Microsoft Authenticator sur votre smartphone.',
        'step_2_title' => 'Étape 2 : Scannez le code QR',
        'step_2_description' => 'Scannez ce code QR avec votre application d\'authentification pour ajouter ce compte.',
        'step_3_title' => 'Étape 3 : Entrez le code de vérification',
        'step_3_description' => 'Entrez le code à 6 chiffres généré par votre application pour finaliser la configuration.',
        'step_4_title' => 'Étape 4 : Enregistrez vos codes de récupération',
        'step_4_description' => 'Ces codes vous permettront d\'accéder à votre compte si vous perdez votre téléphone. Gardez-les en sécurité.',
        'code_label' => 'Code de vérification',
        'code_placeholder' => 'Entrez le code à 6 chiffres',
        'confirm_button' => 'Confirmer et activer',
        'finish_button' => 'Terminer',
        'success_message' => 'L\'authentification à deux facteurs a été activée avec succès !',
        'invalid_code' => 'Le code de vérification est invalide.',
        'download_recovery_codes' => 'Télécharger les codes de récupération',
        'copy_recovery_codes' => 'Copier les codes de récupération',
        'regenerate_recovery_codes' => 'Regénérer les codes de récupération',
    ],

    /*
    |--------------------------------------------------------------------------
    | Social Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during social authentication.
    |
    */

    'social' => [
        'title' => 'Connexion sociale',
        'heading' => 'Connectez-vous avec vos réseaux sociaux',
        'subheading' => 'Utilisez votre compte social pour vous connecter rapidement.',
        'google' => 'Continuer avec Google',
        'facebook' => 'Continuer avec Facebook',
        'twitter' => 'Continuer avec Twitter',
        'github' => 'Continuer avec GitHub',
        'linkedin' => 'Continuer avec LinkedIn',
        'or' => 'ou',
        'success_message' => 'Connexion réussie !',
        'error_message' => 'La connexion sociale a échoué. Veuillez réessayer.',
        'account_not_linked' => 'Ce compte social n\'est pas lié à votre compte.',
        'link_account' => 'Lier le compte',
        'unlink_account' => 'Détacher le compte',
        'account_linked' => 'Le compte social a été lié avec succès.',
        'account_unlinked' => 'Le compte social a été détaché avec succès.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Profile Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used on the profile page.
    |
    */

    'profile' => [
        'title' => 'Profil',
        'heading' => 'Mon profil',
        'subheading' => 'Gérez vos informations personnelles et votre sécurité.',
        'personal_info' => 'Informations personnelles',
        'name_label' => 'Nom complet',
        'email_label' => 'Adresse email',
        'phone_label' => 'Téléphone',
        'address_label' => 'Adresse',
        'bio_label' => 'Biographie',
        'avatar_label' => 'Photo de profil',
        'security' => 'Sécurité',
        'change_password' => 'Changer le mot de passe',
        'current_password_label' => 'Mot de passe actuel',
        'new_password_label' => 'Nouveau mot de passe',
        'password_confirmation_label' => 'Confirmer le mot de passe',
        'two_factor' => 'Authentification à deux facteurs',
        'enable_two_factor' => 'Activer l\'authentification à deux facteurs',
        'disable_two_factor' => 'Désactiver l\'authentification à deux facteurs',
        'regenerate_recovery_codes' => 'Regénérer les codes de récupération',
        'show_recovery_codes' => 'Afficher les codes de récupération',
        'notifications' => 'Notifications',
        'email_notifications' => 'Notifications par email',
        'marketing_emails' => 'Emails marketing',
        'security_alerts' => 'Alertes de sécurité',
        'save_button' => 'Enregistrer les modifications',
        'cancel_button' => 'Annuler',
        'success_message' => 'Votre profil a été mis à jour avec succès !',
        'error_message' => 'Échec de la mise à jour. Veuillez corriger les erreurs.',
        'password_changed' => 'Votre mot de passe a été changé avec succès !',
        'two_factor_enabled' => 'L\'authentification à deux facteurs a été activée.',
        'two_factor_disabled' => 'L\'authentification à deux facteurs a été désactivée.',
    ],

    /*
    |--------------------------------------------------------------------------
    | API Token Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for API token management.
    |
    */

    'api_tokens' => [
        'title' => 'Tokens API',
        'heading' => 'Gérer vos tokens API',
        'subheading' => 'Créez et gérez des tokens pour accéder à l\'API.',
        'create_token' => 'Créer un token',
        'token_name_label' => 'Nom du token',
        'token_name_placeholder' => 'Entrez un nom pour ce token',
        'permissions_label' => 'Permissions',
        'create_button' => 'Créer le token',
        'cancel_button' => 'Annuler',
        'success_message' => 'Le token a été créé avec succès !',
        'error_message' => 'Échec de la création du token.',
        'delete_confirm' => 'Êtes-vous sûr de vouloir supprimer ce token ?',
        'delete_success' => 'Le token a été supprimé avec succès.',
        'copy_token' => 'Copier le token',
        'token_copied' => 'Le token a été copié dans le presse-papiers.',
        'last_used' => 'Dernière utilisation',
        'never_used' => 'Jamais utilisé',
        'expires_at' => 'Expire le',
        'never_expires' => 'N\'expire jamais',
    ],

    /*
    |--------------------------------------------------------------------------
    | Sessions Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for session management.
    |
    */

    'sessions' => [
        'title' => 'Sessions actives',
        'heading' => 'Gérer vos sessions',
        'subheading' => 'Consultez et gérez toutes vos sessions actives.',
        'current_session' => 'Session actuelle',
        'other_sessions' => 'Autres sessions',
        'device' => 'Appareil',
        'browser' => 'Navigateur',
        'ip_address' => 'Adresse IP',
        'location' => 'Localisation',
        'last_activity' => 'Dernière activité',
        'logout_button' => 'Se déconnecter',
        'logout_other_button' => 'Se déconnecter des autres sessions',
        'logout_confirm' => 'Êtes-vous sûr de vouloir vous déconnecter de cette session ?',
        'logout_other_confirm' => 'Êtes-vous sûr de vouloir vous déconnecter de toutes les autres sessions ?',
        'logout_success' => 'Vous avez été déconnecté avec succès.',
        'logout_other_success' => 'Vous avez été déconnecté des autres sessions avec succès.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Browser Sessions Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for browser session management.
    |
    */

    'browser_sessions' => [
        'title' => 'Sessions du navigateur',
        'heading' => 'Sessions du navigateur',
        'subheading' => 'Gérez et révoquez vos sessions actives.',
        'description' => 'Si nécessaire, vous pouvez révoquer toutes vos sessions sur tous vos appareils. Certaines de vos sessions récentes sont listées ci-dessous.',
        'revoke_all' => 'Révoquer toutes les sessions',
        'revoke_confirm' => 'Êtes-vous sûr de vouloir révoquer toutes vos sessions ?',
        'revoke_success' => 'Toutes vos sessions ont été révoquées avec succès.',
        'this_device' => 'Cet appareil',
        'last_active' => 'Dernière activité',
        'revoke' => 'Révoquer',
        'revoke_confirm_single' => 'Êtes-vous sûr de vouloir révoquer cette session ?',
        'revoke_success_single' => 'La session a été révoquée avec succès.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Account Deletion Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for account deletion.
    |
    */

    'delete_account' => [
        'title' => 'Supprimer le compte',
        'heading' => 'Supprimer votre compte',
        'subheading' => 'Une fois votre compte supprimé, toutes ses ressources et données seront définitivement supprimées.',
        'warning' => 'Attention : Cette action est irréversible.',
        'confirm_text' => 'Pour confirmer, veuillez taper DELETE dans la case ci-dessous.',
        'confirm_placeholder' => 'Tapez DELETE pour confirmer',
        'delete_button' => 'Supprimer mon compte',
        'cancel_button' => 'Annuler',
        'success_message' => 'Votre compte a été supprimé avec succès.',
        'error_message' => 'Échec de la suppression du compte. Veuillez réessayer.',
        'invalid_confirmation' => 'Le texte de confirmation est invalide.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Templates Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used in email templates.
    |
    */

    'emails' => [
        'verification' => [
            'subject' => 'Vérifiez votre adresse email',
            'greeting' => 'Bonjour !',
            'intro' => 'Merci de vous être inscrit. Veuillez cliquer sur le bouton ci-dessous pour vérifier votre adresse email :',
            'button' => 'Vérifier l\'adresse email',
            'closing' => 'Cordialement,',
            'signature' => 'L\'équipe de support',
        ],
        'password_reset' => [
            'subject' => 'Réinitialisation du mot de passe',
            'greeting' => 'Bonjour !',
            'intro' => 'Vous recevez cet email car nous avons reçu une demande de réinitialisation du mot de passe pour votre compte.',
            'button' => 'Réinitialiser le mot de passe',
            'closing' => 'Ce lien de réinitialisation du mot de passe expirera dans :count minutes.',
            'signature' => 'Si vous n\'avez pas demandé de réinitialisation du mot de passe, aucune action n\'est requise.',
        ],
        'two_factor_code' => [
            'subject' => 'Votre code de vérification à deux facteurs',
            'greeting' => 'Bonjour !',
            'intro' => 'Voici votre code de vérification à deux facteurs :',
            'code' => 'CODE',
            'closing' => 'Ce code expirera dans 10 minutes.',
            'signature' => 'L\'équipe de support',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Error Messages Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for error messages.
    |
    */

    'errors' => [
        'invalid_credentials' => 'Identifiants invalides',
        'user_not_found' => 'Utilisateur non trouvé',
        'email_not_verified' => 'Adresse email non vérifiée',
        'account_suspended' => 'Compte suspendu',
        'account_banned' => 'Compte banni',
        'too_many_attempts' => 'Trop de tentatives. Veuillez réessayer dans :seconds secondes.',
        'invalid_token' => 'Token invalide',
        'expired_token' => 'Token expiré',
        'invalid_password' => 'Mot de passe invalide',
        'password_mismatch' => 'Les mots de passe ne correspondent pas',
        'weak_password' => 'Le mot de passe est trop faible',
        'email_already_exists' => 'Cette adresse email est déjà utilisée',
        'username_already_exists' => 'Ce nom d\'utilisateur est déjà utilisé',
        'invalid_email' => 'Adresse email invalide',
        'verification_failed' => 'La vérification a échoué',
        'two_factor_required' => 'L\'authentification à deux facteurs est requise',
        'invalid_two_factor_code' => 'Code à deux facteurs invalide',
        'two_factor_disabled' => 'L\'authentification à deux facteurs est désactivée',
        'session_expired' => 'Votre session a expiré',
        'access_denied' => 'Accès refusé',
        'forbidden' => 'Interdit',
        'unauthorized' => 'Non autorisé',
        'server_error' => 'Erreur serveur',
        'network_error' => 'Erreur réseau',
        'maintenance_mode' => 'Le site est en maintenance',
    ],

    /*
    |--------------------------------------------------------------------------
    | Success Messages Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for success messages.
    |
    */

    'success' => [
        'login_successful' => 'Connexion réussie',
        'registration_successful' => 'Inscription réussie',
        'logout_successful' => 'Déconnexion réussie',
        'password_reset_successful' => 'Mot de passe réinitialisé avec succès',
        'email_verified' => 'Adresse email vérifiée avec succès',
        'profile_updated' => 'Profil mis à jour avec succès',
        'password_changed' => 'Mot de passe changé avec succès',
        'two_factor_enabled' => 'Authentification à deux facteurs activée',
        'two_factor_disabled' => 'Authentification à deux facteurs désactivée',
        'account_deleted' => 'Compte supprimé avec succès',
        'token_created' => 'Token API créé avec succès',
        'token_deleted' => 'Token API supprimé avec succès',
        'session_revoked' => 'Session révoquée avec succès',
        'all_sessions_revoked' => 'Toutes les sessions ont été révoquées avec succès',
    ],

    /*
    |--------------------------------------------------------------------------
    | Info Messages Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for informational messages.
    |
    */

    'info' => [
        'please_login' => 'Veuillez vous connecter pour continuer',
        'please_register' => 'Veuillez vous inscrire pour continuer',
        'please_verify_email' => 'Veuillez vérifier votre adresse email',
        'password_requirements' => 'Le mot de passe doit contenir au moins 8 caractères, incluant une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial.',
        'two_factor_info' => 'L\'authentification à deux facteurs ajoute une couche de sécurité supplémentaire à votre compte.',
        'recovery_codes_info' => 'Conservez ces codes de récupération dans un endroit sûr. Ils vous permettront d\'accéder à votre compte si vous perdez votre téléphone.',
        'session_info' => 'Voici la liste de vos sessions actives. Vous pouvez révoquer n\'importe laquelle d\'entre elles.',
    ],
];
