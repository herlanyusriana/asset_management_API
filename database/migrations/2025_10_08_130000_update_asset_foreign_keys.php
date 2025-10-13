<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asset_assignments', function (Blueprint $table) {
            $table->dropForeign(['asset_id']);
            $table->foreign('asset_id')
                ->references('id')
                ->on('assets')
                ->cascadeOnDelete();
        });

        Schema::table('asset_requests', function (Blueprint $table) {
            $table->dropForeign(['asset_id']);
            $table->foreign('asset_id')
                ->references('id')
                ->on('assets')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('asset_assignments', function (Blueprint $table) {
            $table->dropForeign(['asset_id']);
            $table->foreign('asset_id')
                ->references('id')
                ->on('assets');
        });

        Schema::table('asset_requests', function (Blueprint $table) {
            $table->dropForeign(['asset_id']);
            $table->foreign('asset_id')
                ->references('id')
                ->on('assets');
        });
    }
};
