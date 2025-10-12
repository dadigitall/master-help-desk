<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            // Ajouter la colonne deleted_at pour les soft deletes
            $table->softDeletes();
            
            // Corriger l'incohérence : renommer is_active en active pour correspondre au modèle
            $table->renameColumn('is_active', 'active');
            
            // Ajouter les colonnes manquantes qui sont dans le fillable du modèle
            $table->text('description')->nullable()->after('name');
            $table->string('logo')->nullable()->after('address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            // Supprimer la colonne deleted_at
            $table->dropSoftDeletes();
            
            // Restaurer le nom original
            $table->renameColumn('active', 'is_active');
            
            // Supprimer les colonnes ajoutées
            $table->dropColumn(['description', 'logo']);
        });
    }
};
