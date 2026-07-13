<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function __construct(
        private readonly TenantManager $tenantManager,
    ) {}

    /**
     * Display a listing of categories.
     */
    public function index()
    {
        $categories = Category::withCount('products')->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('categories', 'name')
                    ->where('store_id', $this->tenantManager->getStoreId()),
            ],
        ]);

        $slug = Str::slug($request->name);
        $originalSlug = $slug;
        $i = 1;
        while (Category::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $i++;
        }

        Category::create([
            'name' => $request->name,
            'slug' => $slug,
        ]);

        return redirect()->route($this->rolePrefix() . '.categories.index')
            ->with('success', 'Kategori produk berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('categories', 'name')
                    ->where('store_id', $this->tenantManager->getStoreId())
                    ->ignore($category->id),
            ],
        ]);

        $slug = Str::slug($request->name);
        $originalSlug = $slug;
        $i = 1;
        while (Category::where('slug', $slug)->where('id', '!=', $category->id)->exists()) {
            $slug = $originalSlug . '-' . $i++;
        }

        $category->update([
            'name' => $request->name,
            'slug' => $slug,
        ]);

        return redirect()->route($this->rolePrefix() . '.categories.index')
            ->with('success', 'Kategori produk berhasil diperbarui.');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route($this->rolePrefix() . '.categories.index')
            ->with('success', 'Kategori produk berhasil dihapus.');
    }

    /**
     * Store a newly created category via API.
     */
    public function storeApi(Request $request)
    {
        $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('categories', 'name')
                    ->where('store_id', $this->tenantManager->getStoreId()),
            ],
        ]);

        $slug = Str::slug($request->name);
        $originalSlug = $slug;
        $i = 1;
        while (Category::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $i++;
        }

        $category = Category::create([
            'name' => $request->name,
            'slug' => $slug,
        ]);

        $category->products_count = 0;

        return response()->json([
            'success' => true,
            'category' => $category
        ]);
    }

    /**
     * Update the specified category via API.
     */
    public function updateApi(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('categories', 'name')
                    ->where('store_id', $this->tenantManager->getStoreId())
                    ->ignore($category->id),
            ],
        ]);

        $slug = Str::slug($request->name);
        $originalSlug = $slug;
        $i = 1;
        while (Category::where('slug', $slug)->where('id', '!=', $category->id)->exists()) {
            $slug = $originalSlug . '-' . $i++;
        }

        $category->update([
            'name' => $request->name,
            'slug' => $slug,
        ]);

        $category->products_count = $category->products()->count();

        return response()->json([
            'success' => true,
            'category' => $category
        ]);
    }

    /**
     * Remove the specified category via API.
     */
    public function destroyApi($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json(['success' => true]);
    }
}
