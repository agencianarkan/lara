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
        Schema::create('plaza_role_definitions', function (Blueprint $table) {
            $table->unsignedSmallInteger('role_id');
            $table->unsignedSmallInteger('capability_id');
            
            $table->primary(['role_id', 'capability_id']);
            $table->foreign('role_id')->references('id')->on('plaza_roles')->cascadeOnDelete();
            $table->foreign('capability_id')->references('id')->on('plaza_capabilities')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plaza_role_definitions');
    }
};

