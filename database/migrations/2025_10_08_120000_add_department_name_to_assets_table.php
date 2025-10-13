<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->string('department_name')->nullable()->after('asset_category_id');
        });

        DB::statement(
            "UPDATE assets a
                LEFT JOIN asset_categories c ON a.asset_category_id = c.id
             SET a.department_name = c.department_code
             WHERE a.department_name IS NULL OR a.department_name = ''"
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('department_name');
        });
    }
};
