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
        Schema::create('plaza_capabilities', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('module', 30);
            $table->string('slug', 50)->unique();
            $table->string('label', 100);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plaza_capabilities');
    }
};

