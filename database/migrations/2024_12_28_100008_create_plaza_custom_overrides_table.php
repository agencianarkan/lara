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
        Schema::create('plaza_custom_overrides', function (Blueprint $table) {
            $table->foreignId('membership_id')->constrained('plaza_memberships')->cascadeOnDelete();
            $table->unsignedSmallInteger('capability_id');
            $table->boolean('is_granted');

            $table->primary(['membership_id', 'capability_id']);
            $table->foreign('capability_id')->references('id')->on('plaza_capabilities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plaza_custom_overrides');
    }
};

