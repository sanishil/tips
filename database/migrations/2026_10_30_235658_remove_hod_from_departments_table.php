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
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn('hod');
        });
    }

    /**
     * Reverse the migrations. The names that were removed can't come back, so the column returns empty.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->string('hod', 100)->default('')->after('dept_name');
        });
    }
};
