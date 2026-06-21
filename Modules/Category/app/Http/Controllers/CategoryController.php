<?php

namespace Modules\Category\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Category\Http\Requests\StoreCategoryRequest;
use Modules\Category\Http\Requests\UpdateCategoryRequest;
use Modules\Category\Transformers\CategoryResource;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Category::query();

        // Search by name (ensure it's a string)
        if ($request->has('search') && is_string($request->search)) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Search by description
        if ($request->has('search_description') && is_string($request->search_description)) {
            $query->where('description', 'like', '%' . $request->search_description . '%');
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }

        // Filter by created date range
        if ($request->has('created_from')) {
            $query->whereDate('created_at', '>=', $request->created_from);
        }
        if ($request->has('created_to')) {
            $query->whereDate('created_at', '<=', $request->created_to);
        }

        // Filter by updated date range
        if ($request->has('updated_from')) {
            $query->whereDate('updated_at', '>=', $request->updated_from);
        }
        if ($request->has('updated_to')) {
            $query->whereDate('updated_at', '<=', $request->updated_to);
        }

        // Sorting
        $allowedSortFields = ['name', 'created_at', 'updated_at', 'id'];
        $sortBy = $request->input('sort_by', 'id');
        $sortDir = $request->input('sort_dir', 'desc');

        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, in_array(strtolower($sortDir), ['asc', 'desc']) ? $sortDir : 'desc');
        } else {
            $query->orderBy('id', 'desc');
        }

        // Pagination
        $perPage = $request->input('per_page', 15);
        $page = $request->input('page', 1);

        $categories = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'data'    => CategoryResource::collection($categories),
            'pagination' => [
                'current_page' => $categories->currentPage(),
                'per_page' => $categories->perPage(),
                'total' => $categories->total(),
                'last_page' => $categories->lastPage(),
                'from' => $categories->firstItem(),
                'to' => $categories->lastItem(),
            ],
        ]);
    }

    /**
     * Store a newly created category.
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Make sure we have a name to generate a slug from; fall back to timestamp
        $name = $data['name'] ?? $request->input('name') ?? '';
        $data['slug'] = Str::slug($name ?: (string) time());

        // Ensure slug uniqueness (only run if slug is non-empty)
        $originalSlug = $data['slug'];
        $counter = 1;
        if ($data['slug'] !== '') {
            while (Category::where('slug', $data['slug'])->exists()) {
                $data['slug'] = $originalSlug . '-' . $counter++;
            }
        }

        $category = Category::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
            'data'    => new CategoryResource($category),
        ], 201);
    }

    /**
     * Display the specified category with its products.
     */
    public function show(Category $category): JsonResponse
    {
        $category->load('products');

        return response()->json([
            'success' => true,
            'data'    => new CategoryResource($category),
        ]);
    }

    /**
     * Update the specified category.
     */
    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $data = $request->validated();

        // Regenerate slug if name changed
        if (isset($data['name']) && $data['name'] !== $category->name) {
            $data['slug'] = Str::slug($data['name'] ?? '');

            $originalSlug = $data['slug'];
            $counter = 1;
            if ($data['slug'] !== '') {
                while (Category::where('slug', $data['slug'])->where('id', '!=', $category->id)->exists()) {
                    $data['slug'] = $originalSlug . '-' . $counter++;
                }
            }
        }

        $category->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
            'data'    => new CategoryResource($category),
        ]);
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Category $category): JsonResponse
    {
        // Prevent deletion if category has products
        if ($category->products()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category that has products. Remove or reassign products first.',
            ], 409);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully',
        ]);
    }
}
