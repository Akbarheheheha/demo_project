<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create categories table
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        // 2. Add category_id to products
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete()->after('name');
        });

        // 3. Migrate existing category text data to categories table
        $oldProducts = DB::table('products')->select('id', 'category')->get();
        $categoriesMap = [];

        foreach ($oldProducts as $prod) {
            $catName = trim($prod->category ?? '');
            if ($catName === '') {
                $catName = 'Umum';
            }

            if (!isset($categoriesMap[$catName])) {
                $slug = Str::slug($catName);
                $originalSlug = $slug;
                $i = 1;
                while (DB::table('categories')->where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $i++;
                }

                $catId = DB::table('categories')->insertGetId([
                    'name' => $catName,
                    'slug' => $slug,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $categoriesMap[$catName] = $catId;
            }

            DB::table('products')->where('id', $prod->id)->update([
                'category_id' => $categoriesMap[$catName]
            ]);
        }

        // 4. Drop old category column
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Add back category column
        Schema::table('products', function (Blueprint $table) {
            $table->string('category')->default('Sembako')->after('name');
        });

        // 2. Restore category string representation
        $products = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('products.id', 'categories.name')
            ->get();

        foreach ($products as $prod) {
            DB::table('products')->where('id', $prod->id)->update([
                'category' => $prod->name
            ]);
        }

        // 3. Drop relationship and categories table
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });

        Schema::dropIfExists('categories');
    }
};
