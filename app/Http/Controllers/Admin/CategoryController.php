<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Category::class);

        $categories = Category::query()
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('admin.categories', [
            'categories' => $categories,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Category::class);

        return view('admin.categories-create');
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $this->authorize('create', Category::class);

        $validated = $request->validated();

        $slug = ($validated['slug'] ?? null) ?: $this->uniqueSlug($validated['name']);

        Category::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'is_active' => (bool) $validated['is_active'],
        ]);

        return redirect()->route('admin.categories.index')->with('status', 'category-created');
    }

    public function edit(Category $category): View
    {
        $this->authorize('update', $category);

        return view('admin.categories-edit', [
            'category' => $category,
        ]);
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $this->authorize('update', $category);

        $validated = $request->validated();

        $slug = ($validated['slug'] ?? null) ?: $this->uniqueSlug($validated['name'], $category->id);

        $category->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'is_active' => (bool) $validated['is_active'],
        ]);

        return redirect()->route('admin.categories.index')->with('status', 'category-updated');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->authorize('delete', $category);

        $category->delete();

        return redirect()->route('admin.categories.index')->with('status', 'category-deleted');
    }

    protected function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);

        if ($base === '') {
            $base = 'kategori';
        }

        $slug = $base;
        $i = 2;

        while (true) {
            $query = Category::query()->where('slug', $slug);
            if ($ignoreId) {
                $query->whereKeyNot($ignoreId);
            }

            if (! $query->exists()) {
                return $slug;
            }

            $slug = $base.'-'.$i;
            $i++;
        }
    }
}
