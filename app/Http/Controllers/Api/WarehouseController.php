<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Warehouse\StoreWarehouseRequest;
use App\Http\Requests\Api\Warehouse\UpdateWarehouseRequest;
use App\Models\Warehouse;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Warehouse::query()->with('manager');

        if ($search = request('search')) {
            $query->where(function ($query) use ($search) {
                $query->where('code', 'like', "%$search%")
                    ->orWhere('name', 'like', "%$search%")
                    ->orWhere('address', 'like', "%$search%");
            });
        }

        if ($status = request('status')) {
            $query->where('status', $status);
        }

        $warehouses = $query->get();

        return response()->json($warehouses);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWarehouseRequest $request)
    {
        $data = $request->validated();
        $warehouse = Warehouse::create($data);
        $warehouse->load('manager');

        return response()->json([
            'message' => 'Tạo kho hàng thành công',
            'warehouse' => $warehouse,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $warehouse = Warehouse::with('manager')->find($id);
        if (! $warehouse) {
            return response()->json(['message' => 'Kho hàng không tồn tại'], 404);
        }

        return response()->json($warehouse);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWarehouseRequest $request, string $id)
    {
        $warehouse = Warehouse::find($id);
        if (! $warehouse) {
            return response()->json(['message' => 'Kho hàng không tồn tại'], 404);
        }

        $data = $request->validated();
        $warehouse->update($data);
        $warehouse->load('manager');

        return response()->json([
            'message' => 'Cập nhật kho hàng thành công',
            'warehouse' => $warehouse,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $warehouse = Warehouse::find($id);
        if (! $warehouse) {
            return response()->json(['message' => 'Kho hàng không tồn tại'], 404);
        }

        $warehouse->delete();

        return response()->json([
            'message' => 'Xóa kho hàng thành công',
        ]);
    }
}
