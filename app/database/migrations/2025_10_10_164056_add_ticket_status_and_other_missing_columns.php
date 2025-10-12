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
            // Colonnes manquantes
            $table->string('status')->default('open')->after('description'); // open, in_progress, resolved, closed
            $table->timestamp('resolved_at')->nullable()->after('deleted_at');
            $table->timestamp('closed_at')->nullable()->after('resolved_at');
            
            // Index pour améliorer les performances
            $table->index(['status']);
            $table->index(['created_at']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['status', 'created_at']);
            
            $table->dropColumn([
                'status',
                'resolved_at',
                'closed_at'
            ]);
        });
    }
};
