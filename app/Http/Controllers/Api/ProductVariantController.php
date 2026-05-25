<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ProductVariant\StoreProductVariantRequest;
use App\Http\Requests\Api\ProductVariant\UpdateProductVariantRequest;
use App\Models\ProductVariant;

class ProductVariantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = ProductVariant::query()->with('product');

        if ($search = request('search')) {
            $query->where(function ($query) use ($search) {
                $query->where('sku', 'like', "%$search%")
                    ->orWhere('barcode', 'like', "%$search%")
                    ->orWhere('variant_name', 'like', "%$search%");
            });
        }
        if ($productId = request('product_id')) {
            $query->where('product_id', $productId);
        }
        if ($status = request('status')) {
            $query->where('status', $status);
        }

        $variants = $query->get();

        return response()->json($variants);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductVariantRequest $request)
    {
        $data = $request->validated();
        $variant = ProductVariant::create($data);
        $variant->load('product');

        return response()->json([
            'message' => 'Tạo biến thể sản phẩm thành công',
            'variant' => $variant,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $variant = ProductVariant::with('product')->find($id);
        if (! $variant) {
            return response()->json(['message' => 'Biến thể sản phẩm không tồn tại'], 404);
        }

        return response()->json($variant);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductVariantRequest $request, string $id)
    {
        $variant = ProductVariant::find($id);
        if (! $variant) {
            return response()->json(['message' => 'Biến thể sản phẩm không tồn tại'], 404);
        }

        $data = $request->validated();
        $variant->update($data);
        $variant->load('product');

        return response()->json([
            'message' => 'Cập nhật biến thể sản phẩm thành công',
            'variant' => $variant,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $variant = ProductVariant::find($id);
        if (! $variant) {
            return response()->json(['message' => 'Biến thể sản phẩm không tồn tại'], 404);
        }

        $variant->delete();

        return response()->json([
            'message' => 'Xóa biến thể sản phẩm thành công',
        ]);
    }
}
