<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE transactions MODIFY payment_method VARCHAR(255) NOT NULL DEFAULT 'Tunai'");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE transactions MODIFY payment_method VARCHAR(255) NOT NULL');
    }
};
