<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Services\ProductImageService;

class ProductController extends Controller
{
    public function __construct(
        private ProductImageService $imageService
    ) {}

    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
        }

        $products = $query->paginate(10);

        return Inertia::render('Products/Index', [
            'products' => $products,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Products/Form', [
            'product' => null,
            'mode' => 'create',
        ]);
    }

    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $validated['image'] = $this->imageService->storeImage($request->file('image'));
        }

        $product = Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        return Inertia::render('Products/Form', [
            'product' => $product,
            'mode' => 'edit',
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            if ($product->image) {
                $this->imageService->deleteImage($product->image);
            }
            $validated['image'] = $this->imageService->storeImage($request->file('image'));
        }

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            $this->imageService->deleteImage($product->image);
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }

    public function archived(Request $request)
    {
        $query = Product::onlyTrashed();

        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
        }

        $products = $query->paginate(10);

        return Inertia::render('Products/Archived', [
            'products' => $products,
            'filters' => $request->only(['search']),
        ]);
    }

    public function restore($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        $product->restore();

        return redirect()->route('products.archived')->with('success', 'Product restored successfully.');
    }

    public function forceDelete($id)
    {
        $product = Product::withTrashed()->findOrFail($id);

        if ($product->image) {
            $this->imageService->deleteImage($product->image);
        }

        $product->forceDelete();

        return redirect()->route('products.archived')->with('success', 'Product permanently deleted.');
    }

    public function show(Product $product)
    {
        return Inertia::render('Products/Show', [
            'product' => $product,
        ]);
    }
}
