<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
| A department runs under a course and, optionally, one of its sub-courses. The course is nullable
| here only because departments added before this have none; the form requires one from now on.
*/
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->foreignId('course_id')->nullable()->after('hod')->constrained()->restrictOnDelete();
            $table->foreignId('sub_course_id')->nullable()->after('course_id')->constrained()->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sub_course_id');
            $table->dropConstrainedForeignId('course_id');
        });
    }
};
