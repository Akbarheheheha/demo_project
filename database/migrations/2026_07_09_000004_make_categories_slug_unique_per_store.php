<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table): void {
            $table->dropUnique('categories_slug_unique');

            $table->unique(['store_id', 'slug'], 'categories_store_id_slug_unique');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table): void {
            $table->dropUnique('categories_store_id_slug_unique');

            $table->unique('slug');
        });
    }
};