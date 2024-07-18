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
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('admin_number')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable()->comment('Middle Name');
            $table->string('last_name')->nullable();
            $table->enum('role', ['super_admin', 'admin', 'staff'])->nullable();
            $table->string('mobile_number')->nullable();
            $table->date('birthday')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
