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
            $table->string('processor_name')->nullable()->after('model');
            $table->string('ram_capacity')->nullable()->after('processor_name');
            $table->string('storage_type')->nullable()->after('ram_capacity');
            $table->string('storage_brand')->nullable()->after('storage_type');
            $table->string('storage_capacity')->nullable()->after('storage_brand');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn([
                'processor_name',
                'ram_capacity',
                'storage_type',
                'storage_brand',
                'storage_capacity',
            ]);
        });
    }
};
