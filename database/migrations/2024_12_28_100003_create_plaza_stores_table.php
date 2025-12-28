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
        Schema::create('plaza_stores', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('domain_url', 150);
            $table->enum('platform_type', ['woocommerce', 'shopify', 'jumpseller', 'custom'])->default('woocommerce');
            $table->json('connection_config')->nullable();
            $table->string('plaza_api_key', 64)->unique();
            $table->foreignId('owner_id')->nullable()->constrained('plaza_users')->nullOnDelete();
            $table->string('logo_url', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plaza_stores');
    }
};

