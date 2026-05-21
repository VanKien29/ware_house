<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ModelKitManufacturer\StoreModelKitManufacturerRequest;
use App\Http\Requests\Api\ModelKitManufacturer\UpdateModelKitManufacturerRequest;
use App\Models\ModelKitManufacturer;

class ModelKitManufacturerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = ModelKitManufacturer::query();
        if ($search = request('search')) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%$search%")
                    ->orWhere('country', 'like', "%$search%");
            });
        }
        $manufacturers = $query->get();

        return response()->json($manufacturers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreModelKitManufacturerRequest $request)
    {
        $data = $request->validated();
        $manufacturer = ModelKitManufacturer::create($data);

        return response()->json([
            'message' => 'Tạo nhà sản xuất thành công',
            'manufacturer' => $manufacturer,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $manufacturer = ModelKitManufacturer::find($id);
        if (! $manufacturer) {
            return response()->json(['message' => 'Nhà sản xuất không tồn tại'], 404);
        }

        return response()->json($manufacturer);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateModelKitManufacturerRequest $request, string $id)
    {
        $manufacturer = ModelKitManufacturer::find($id);
        if (! $manufacturer) {
            return response()->json(['message' => 'Nhà sản xuất không tồn tại'], 404);
        }

        $data = $request->validated();
        $manufacturer->update($data);

        return response()->json([
            'message' => 'Cập nhật nhà sản xuất thành công',
            'manufacturer' => $manufacturer,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $manufacturer = ModelKitManufacturer::find($id);
        if (! $manufacturer) {
            return response()->json(['message' => 'Nhà sản xuất không tồn tại'], 404);
        }

        $manufacturer->delete();

        return response()->json(['message' => 'Xóa nhà sản xuất thành công']);
    }
}
