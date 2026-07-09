<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_methods', function (Blueprint $table): void {
            $table->dropUnique('payment_methods_nama_metode_unique');

            $table->unique(['store_id', 'nama_metode'], 'payment_methods_store_id_nama_metode_unique');
        });
    }

    public function down(): void
    {
        Schema::table('payment_methods', function (Blueprint $table): void {
            $table->dropUnique('payment_methods_store_id_nama_metode_unique');

            $table->unique('nama_metode');
        });
    }
};