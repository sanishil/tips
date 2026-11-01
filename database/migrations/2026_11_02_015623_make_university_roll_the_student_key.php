<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
| Students are now saved and looked up by university roll number; the college ID is gone. A student saved
| without a roll number keeps their old college ID in its place, so no record is lost.
*/
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('students')->whereNull('tu_roll')->update(['tu_roll' => DB::raw('std_id')]);

        Schema::table('students', function (Blueprint $table) {
            $table->dropUnique(['std_id']);
            $table->dropColumn('std_id');
            $table->string('tu_roll', 30)->nullable(false)->unique()->change();
        });
    }

    /**
     * Reverse the migrations. The roll number stands in for the college ID.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('std_id', 30)->nullable()->after('id');
            $table->dropUnique(['tu_roll']);
            $table->string('tu_roll', 30)->nullable()->change();
        });

        DB::table('students')->update(['std_id' => DB::raw('tu_roll')]);

        Schema::table('students', function (Blueprint $table) {
            $table->string('std_id', 30)->nullable(false)->unique()->change();
        });
    }
};
