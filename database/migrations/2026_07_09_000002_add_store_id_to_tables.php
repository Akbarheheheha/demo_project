<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLES = [
        'users',
        'products',
        'transactions',
        'expenses',
        'settings',
        'categories',
        'payment_methods',
        'activity_logs',
    ];

    public function up(): void
    {
        foreach (self::TABLES as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                if (!Schema::hasColumn($tableName, 'store_id')) {
                    $table->unsignedBigInteger('store_id')
                        ->nullable()
                        ->after('id')
                        ->index();
                }
            });
        }

        foreach (self::TABLES as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                $foreignKeyName = $tableName . '_store_id_foreign';

                $foreignKeys = collect(Schema::getForeignKeys($tableName))
                    ->pluck('name')
                    ->toArray();

                if (!in_array($foreignKeyName, $foreignKeys, true)) {
                    $table->foreign('store_id', $foreignKeyName)
                        ->references('id')
                        ->on('stores')
                        ->cascadeOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        foreach (self::TABLES as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                if (Schema::hasColumn($tableName, 'store_id')) {
                    $table->dropConstrainedForeignId('store_id');
                }
            });
        }
    }
};