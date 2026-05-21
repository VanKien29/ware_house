<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Unit\StoreUnitRequest;
use App\Http\Requests\Api\Unit\UpdateUnitRequest;
use App\Models\Unit;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Unit = Unit::all();

        return response()->json($Unit);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUnitRequest $request)
    {
        $data = $request->validated();
        $Unit = Unit::create($data);

        return response()->json([
            'message' => 'Tạo đơn vị thành công',
            'unit' => $Unit,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $Unit = Unit::find($id);
        if (! $Unit) {
            return response()->json(['message' => 'Đơn vị không tồn tại'], 404);
        }

        return response()->json($Unit);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUnitRequest $request, string $id)
    {
        $Unit = Unit::find($id);
        if (! $Unit) {
            return response()->json(['message' => 'Đơn vị không tồn tại'], 404);
        }

        $data = $request->validated();

        $Unit->update($data);

        return response()->json([
            'message' => 'Cập nhật đơn vị thành công',
            'unit' => $Unit,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $Unit = Unit::find($id);
        if (! $Unit) {
            return response()->json(['message' => 'Đơn vị không tồn tại'], 404);
        }

        $Unit->delete();

        return response()->json(['message' => 'Xóa đơn vị thành công']);
    }
}
