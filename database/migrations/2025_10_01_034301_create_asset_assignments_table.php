<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets');
            $table->foreignId('assigned_to_user_id')->nullable()->constrained('users');
            $table->foreignId('assigned_by_user_id')->constrained('users');
            $table->string('department_code');
            $table->timestamp('assigned_at');
            $table->timestamp('expected_return_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->text('condition_on_return')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['department_code']);
            $table->index(['assigned_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_assignments');
    }
};
