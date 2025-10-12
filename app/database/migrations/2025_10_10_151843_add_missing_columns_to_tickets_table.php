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
        Schema::table('tickets', function (Blueprint $table) {
            // Colonnes manquantes d'après la structure actuelle
            $table->unsignedBigInteger('priority_id')->nullable();
            $table->unsignedBigInteger('type_id')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->softDeletes(); // Ajoute la colonne deleted_at pour SoftDeletes
            
            // Index pour améliorer les performances
            $table->index(['priority_id']);
            $table->index(['type_id']);
            $table->index(['assigned_to']);
            $table->index(['created_by']);
            $table->index(['company_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropSoftDeletes(); // Supprime la colonne deleted_at
            
            $table->dropIndex(['priority_id']);
            $table->dropIndex(['type_id']);
            $table->dropIndex(['assigned_to']);
            $table->dropIndex(['created_by']);
            $table->dropIndex(['company_id']);
            
            $table->dropColumn([
                'priority_id',
                'type_id',
                'assigned_to',
                'created_by',
                'company_id'
            ]);
        });
    }
};
