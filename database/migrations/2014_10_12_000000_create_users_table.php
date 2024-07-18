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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('client_number')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable()->comment('Middle Name');
            $table->string('last_name')->nullable();
            $table->date('birthday')->nullable();
            $table->string('mobile_number')->nullable();
            $table->json('projects')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->timestamps(); // created_at and updated_at
            $table->softDeletes(); // deleted_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
