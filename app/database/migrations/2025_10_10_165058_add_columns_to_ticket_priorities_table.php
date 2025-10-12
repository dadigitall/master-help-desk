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
        Schema::table('ticket_priorities', function (Blueprint $table) {
            $table->string('name')->unique(); // urgent, high, medium, low
            $table->string('color')->nullable(); // hex color code
            $table->integer('level')->unique(); // 1=low, 2=medium, 3=high, 4=urgent
            $table->boolean('is_default')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_priorities', function (Blueprint $table) {
            $table->dropColumn(['name', 'color', 'level', 'is_default']);
        });
    }
};
