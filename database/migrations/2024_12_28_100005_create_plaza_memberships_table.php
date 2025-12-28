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
        Schema::create('plaza_memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('plaza_users')->cascadeOnDelete();
            $table->foreignId('store_id')->constrained('plaza_stores')->cascadeOnDelete();
            $table->unsignedSmallInteger('role_id');
            $table->boolean('is_custom_mode')->default(false);
            $table->foreignId('invited_by')->nullable()->constrained('plaza_users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('role_id')->references('id')->on('plaza_roles')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plaza_memberships');
    }
};

