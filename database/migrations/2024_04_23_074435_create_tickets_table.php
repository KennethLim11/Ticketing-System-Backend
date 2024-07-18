<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->nullable();
            $table->date('reported_date')->nullable();
            $table->enum('type', ['System Issue', 'User-related Issue', 'Others'])->nullable();
            $table->string('type_other')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['Open', 'On-going', 'Closed'])->default('Open');
            $table->string('project')->nullable();
            $table->string('file_path_url')->nullable();
            $table->morphs('ticketable');
            $table->timestamps();
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
