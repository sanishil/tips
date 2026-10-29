<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
| Teachers' typed designation becomes a link to the designations table. Any designations already
| typed are copied into that table first, so no teacher loses theirs.
*/
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->foreignId('designation_id')->nullable()->after('department_id')->constrained()->restrictOnDelete();
        });

        foreach (DB::table('teachers')->distinct()->pluck('designation') as $name) {
            DB::table('designations')->insertOrIgnore(['name' => $name, 'created_at' => now(), 'updated_at' => now()]);
            DB::table('teachers')->where('designation', $name)
                ->update(['designation_id' => DB::table('designations')->where('name', $name)->value('id')]);
        }

        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn('designation');
        });

        Schema::table('teachers', function (Blueprint $table) {
            $table->foreignId('designation_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->string('designation', 60)->default('')->after('department_id');
        });

        foreach (DB::table('designations')->get(['id', 'name']) as $designation) {
            DB::table('teachers')->where('designation_id', $designation->id)->update(['designation' => $designation->name]);
        }

        Schema::table('teachers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('designation_id');
        });
    }
};
