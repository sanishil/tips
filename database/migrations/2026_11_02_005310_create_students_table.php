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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('std_id', 15)->unique();
            $table->string('tu_roll', 30)->nullable();
            $table->string('title', 10);
            $table->string('name', 100);
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('semester');
            $table->string('section', 1)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
