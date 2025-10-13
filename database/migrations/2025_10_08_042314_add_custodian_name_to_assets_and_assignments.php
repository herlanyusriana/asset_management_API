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
        Schema::table('assets', function (Blueprint $table) {
            $table->string('current_custodian_name')->nullable()->after('current_custodian_id');
        });

        Schema::table('asset_assignments', function (Blueprint $table) {
            $table->string('assigned_to_name')->nullable()->after('assigned_to_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_assignments', function (Blueprint $table) {
            $table->dropColumn('assigned_to_name');
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('current_custodian_name');
        });
    }
};
