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
        Schema::create('company_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['owner','manager'])->default('manager');
            $table->timestamps();

            $table->unique(['company_id','user_id']);
        });
        // NOTE: this pivot table is currently optional since users.company_id/role
        // are stored directly on the users table. It may be removed in future
        // if duplicate data isn't required.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_users');
    }
};
