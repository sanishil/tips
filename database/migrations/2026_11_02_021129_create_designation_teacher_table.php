<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
| A teacher can now hold several designations (e.g. Lecturer (SG-I) and Principal (i/c)). Each teacher's
| current designation is copied into the new table before the old column goes.
*/
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('designation_teacher', function (Blueprint $table) {
            $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();
            $table->foreignId('designation_id')->constrained()->restrictOnDelete();

            $table->primary(['teacher_id', 'designation_id']);
        });

        DB::table('designation_teacher')->insertUsing(
            ['teacher_id', 'designation_id'],
            DB::table('teachers')->select('id', 'designation_id')->whereNotNull('designation_id'),
        );

        Schema::table('teachers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('designation_id');
        });
    }

    /**
     * Reverse the migrations. A teacher with several designations keeps the first one by name.
     */
    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->foreignId('designation_id')->nullable()->after('department_id')->constrained()->restrictOnDelete();
        });

        DB::table('teachers')->update([
            'designation_id' => DB::table('designation_teacher')
                ->join('designations', 'designations.id', '=', 'designation_teacher.designation_id')
                ->whereColumn('designation_teacher.teacher_id', 'teachers.id')
                ->orderBy('designations.name')
                ->limit(1)
                ->select('designations.id'),
        ]);

        Schema::dropIfExists('designation_teacher');
    }
};
