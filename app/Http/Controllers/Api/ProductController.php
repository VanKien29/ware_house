<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Product\StoreProductRequest;
use App\Http\Requests\Api\Product\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Product::query();

        if ($search = request('search')) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%$search%")
                    ->orWhere('kit_code', 'like', "%$search%")
                    ->orWhere('grade', 'like', "%$search%")
                    ->orWhere('scale', 'like', "%$search%");
            });
        }

        if ($categoryId = request('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($manufacturerId = request('manufacturer_id')) {
            $query->where('manufacturer_id', $manufacturerId);
        }

        if ($seriesId = request('series_id')) {
            $query->where('series_id', $seriesId);
        }

        if ($grade = request('grade')) {
            $query->where('grade', $grade);
        }

        if ($scale = request('scale')) {
            $query->where('scale', $scale);
        }

        if ($status = request('status')) {
            $query->where('status', $status);
        }

        $products = $query->with(['category', 'baseUnit', 'manufacturer', 'series'])->get();

        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();
        $baseSlug = Str::slug($data['name']);
        $slug = $baseSlug;
        $count = 1;

        while (Product::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $count;
            $count++;
        }

        $data['slug'] = $slug;
        $product = Product::create($data);
        $product->load(['category', 'baseUnit', 'manufacturer', 'series']);

        return response()->json([
            'message' => 'Tạo sản phẩm thành công',
            'product' => $product,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::with(['category', 'baseUnit', 'manufacturer', 'series'])->find($id);
        if (! $product) {
            return response()->json(['message' => 'Sản phẩm không tồn tại'], 404);
        }

        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, string $id)
    {
        $product = Product::find($id);
        if (! $product) {
            return response()->json(['message' => 'Sản phẩm không tồn tại'], 404);
        }

        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);

        $product->update($data);
        $product->load(['category', 'baseUnit', 'manufacturer', 'series']);

        return response()->json([
            'message' => 'Cập nhật sản phẩm thành công',
            'product' => $product,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);
        if (! $product) {
            return response()->json(['message' => 'Sản phẩm không tồn tại'], 404);
        }

        $product->delete();

        return response()->json(['message' => 'Xóa sản phẩm thành công']);
    }
}
