<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
| A subject now belongs to a course directly. Its sub-course is only set when the course has
| sub-courses (UG → Nursing); for a course without any (PG) it stays empty.
*/
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->foreignId('course_id')->nullable()->after('department_id')->constrained()->restrictOnDelete();

            /* MySQL uses the old unique index for the sub-course foreign key, so give it its own first. */
            $table->index('sub_course_id');
        });

        DB::table('subjects')->update([
            'course_id' => DB::table('sub_courses')->select('course_id')->whereColumn('sub_courses.id', 'subjects.sub_course_id'),
        ]);

        Schema::table('subjects', function (Blueprint $table) {
            $table->dropUnique(['sub_course_id', 'semester', 'name']);
            $table->foreignId('course_id')->nullable(false)->change();
            $table->foreignId('sub_course_id')->nullable()->change();
            $table->unique(['course_id', 'sub_course_id', 'semester', 'name']);
        });
    }

    /**
     * Reverse the migrations. Subjects without a sub-course can't fit the old shape, so they're removed.
     */
    public function down(): void
    {
        DB::table('subjects')->whereNull('sub_course_id')->delete();

        Schema::table('subjects', function (Blueprint $table) {
            $table->dropUnique(['course_id', 'sub_course_id', 'semester', 'name']);
            $table->foreignId('sub_course_id')->nullable(false)->change();
            $table->unique(['sub_course_id', 'semester', 'name']);
            $table->dropIndex(['sub_course_id']);
            $table->dropConstrainedForeignId('course_id');
        });
    }
};
