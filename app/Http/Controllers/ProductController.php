<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = auth()->user()
            ->products()
            ->with('category') 
            ->where(function ($query) {
                if ($search = request()->search) {
                    $query->where('name', 'like', '%' . $search. '%')
                        ->orWhereHas('category', function ($query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%');
                    });
                }
            })
            ->when(!request()->query('sort_by'), function ($query) {
                $query->latest();
            })
            ->when(function () {
                $s = request()->query('sort_by');
                return $s && in_array(ltrim($s, '-'), ['name','price','weight']); // ตัด '-' ����อ��เ������
            }, function ($query) {
                $sortBy   = request()->query('sort_by');
                $field    = ltrim($sortBy, '-');
                $direction= str_starts_with($sortBy, '-') ? 'desc' : 'asc';
                $query->orderBy($field, $direction);
            })
            ->paginate(5)
            ->withQueryString();

        return inertia('Product/Index', [
            'products' => $products->toResourceCollection(),
            'query' => (object) request()->query(),
        ]);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return inertia('Product/Create', [
            'categories' => $categories->toResourceCollection()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * StoreProductRequest
     */
    public function store(StoreProductRequest  $request)
    {
        $request->user()->products()->create($request->validated());
        return redirect()->route('products.index')
            ->with('message', 'Product has been created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
     
        return inertia('Product/Show', [
            'product' => $product->load('category')->toResource(),
        ]);
 
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();

        return inertia('Product/Edit', [
            'product' => $product->load('category')->toResource(),
            'categories' => $categories->toResourceCollection()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        return redirect()->route('products.index')
            ->with('message', 'Product has been updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')
            ->with('message', 'Product has been deleted successfully.');
    }
}
