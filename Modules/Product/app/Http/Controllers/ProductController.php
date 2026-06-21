<?php

namespace Modules\Product\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Product\Http\Requests\StoreProductRequest;
use Modules\Product\Http\Requests\UpdateProductRequest;
use Modules\Product\Transformers\ProductResource;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::with('category');

        // Search by name (ensure it's a string)
        if ($request->has('search') && is_string($request->search)) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Search by description
        if ($request->has('search_description') && is_string($request->search_description)) {
            $query->where('description', 'like', '%' . $request->search_description . '%');
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }

        // Filter by price range
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter by stock range
        if ($request->has('min_stock')) {
            $query->where('stock', '>=', $request->min_stock);
        }
        if ($request->has('max_stock')) {
            $query->where('stock', '<=', $request->max_stock);
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
        $allowedSortFields = ['name', 'price', 'stock', 'created_at', 'updated_at', 'id'];
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

        $products = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'data'    => ProductResource::collection($products),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'last_page' => $products->lastPage(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
            ],
        ]);
    }

    /**
     * Store a newly created product.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Make sure we have a name to generate a slug from; fall back to timestamp
        $name = $data['name'] ?? $request->input('name') ?? '';
        $data['slug'] = Str::slug($name ?: (string) time());

        // Ensure slug uniqueness (only run if slug is non-empty)
        $originalSlug = $data['slug'];
        $counter = 1;
        if ($data['slug'] !== '') {
            while (Product::where('slug', $data['slug'])->exists()) {
                $data['slug'] = $originalSlug . '-' . $counter++;
            }
        }

        $product = Product::create($data);
        $product->load('category');

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data'    => new ProductResource($product),
        ], 201);
    }

    /**
     * Display the specified product with its category.
     */
    public function show(Product $product): JsonResponse
    {
        $product->load('category');

        return response()->json([
            'success' => true,
            'data'    => new ProductResource($product),
        ]);
    }

    /**
     * Update the specified product.
     */
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $data = $request->validated();

        // Regenerate slug if name changed
        if (isset($data['name']) && $data['name'] !== $product->name) {
            $data['slug'] = Str::slug($data['name'] ?? '');

            $originalSlug = $data['slug'];
            $counter = 1;
            if ($data['slug'] !== '') {
                while (Product::where('slug', $data['slug'])->where('id', '!=', $product->id)->exists()) {
                    $data['slug'] = $originalSlug . '-' . $counter++;
                }
            }
        }

        $product->update($data);
        $product->load('category');

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data'    => new ProductResource($product),
        ]);
    }

    /**
     * Remove the specified product.
     */
    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully',
        ]);
    }
}
