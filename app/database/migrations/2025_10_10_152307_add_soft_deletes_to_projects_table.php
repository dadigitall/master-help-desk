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
        Schema::table('projects', function (Blueprint $table) {
            // Ajouter la colonne deleted_at pour SoftDeletes
            $table->softDeletes();
            
            // Corriger les incohérences avec le modèle
            // Renommer user_id en created_by si nécessaire
            if (Schema::hasColumn('projects', 'user_id') && !Schema::hasColumn('projects', 'created_by')) {
                $table->renameColumn('user_id', 'created_by');
            }
            
            // Ajouter les colonnes manquantes du modèle
            if (!Schema::hasColumn('projects', 'icon_id')) {
                $table->foreignId('icon_id')->nullable()->constrained()->onDelete('set null');
            }
            
            // Corriger le nom de la colonne is_active en active
            if (Schema::hasColumn('projects', 'is_active') && !Schema::hasColumn('projects', 'active')) {
                $table->renameColumn('is_active', 'active');
            }
            
            // S'assurer que la colonne created_by a la bonne contrainte
            if (Schema::hasColumn('projects', 'created_by')) {
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropSoftDeletes();
            
            // Restaurer les noms originaux si nécessaire
            if (Schema::hasColumn('projects', 'created_by') && !Schema::hasColumn('projects', 'user_id')) {
                $table->renameColumn('created_by', 'user_id');
            }
            
            if (Schema::hasColumn('projects', 'active') && !Schema::hasColumn('projects', 'is_active')) {
                $table->renameColumn('active', 'is_active');
            }
            
            if (Schema::hasColumn('projects', 'icon_id')) {
                $table->dropForeign(['icon_id']);
                $table->dropColumn('icon_id');
            }
        });
    }
};
