<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ProductCategory\StoreProductCategoryRequest;
use App\Http\Requests\Api\ProductCategory\UpdateProductCategoryRequest;
use App\Models\ProductCategory;
use Illuminate\Support\Str;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $productCategories = ProductCategory::all();

        return response()->json($productCategories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductCategoryRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);

        $productCategory = ProductCategory::create($data);

        return response()->json([
            'message' => 'Tạo danh mục sản phẩm thành công',
            'product_category' => $productCategory,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $productCategory = ProductCategory::findOrFail($id);

        return response()->json($productCategory);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductCategoryRequest $request, string $id)
    {
        $productCategory = ProductCategory::findOrFail($id);
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);

        $productCategory->update($data);

        return response()->json([
            'message' => 'Cập nhật danh mục sản phẩm thành công',
            'product_category' => $productCategory,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $productCategory = ProductCategory::findOrFail($id);
        $productCategory->delete();

        return response()->json([
            'message' => 'Xóa danh mục sản phẩm thành công',
        ]);
    }
}
