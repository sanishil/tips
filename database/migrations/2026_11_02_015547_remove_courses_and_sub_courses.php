<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
| Courses and sub-courses are gone. A department is just a name, and a subject belongs to a department and a
| semester. Going back restores the empty tables and columns, not the data that was in them.
*/
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropForeign(['sub_course_id']);
        });

        Schema::table('subjects', function (Blueprint $table) {
            $table->dropUnique(['course_id', 'sub_course_id', 'semester', 'name']);
            $table->dropIndex(['sub_course_id']);
            $table->dropColumn(['course_id', 'sub_course_id']);
            $table->unique(['department_id', 'semester', 'name']);
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sub_course_id');
            $table->dropConstrainedForeignId('course_id');
        });

        Schema::dropIfExists('sub_courses');
        Schema::dropIfExists('courses');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60)->unique();
            $table->timestamps();
        });

        Schema::create('sub_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->restrictOnDelete();
            $table->string('name', 60);
            $table->timestamps();

            $table->unique(['course_id', 'name']);
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->foreignId('course_id')->nullable()->after('dept_name')->constrained()->restrictOnDelete();
            $table->foreignId('sub_course_id')->nullable()->after('course_id')->constrained()->restrictOnDelete();
        });

        Schema::table('subjects', function (Blueprint $table) {
            $table->dropUnique(['department_id', 'semester', 'name']);
            $table->foreignId('course_id')->nullable()->after('department_id')->constrained()->restrictOnDelete();
            $table->foreignId('sub_course_id')->nullable()->after('course_id')->constrained()->restrictOnDelete();
            $table->unique(['course_id', 'sub_course_id', 'semester', 'name']);
        });
    }
};
