<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ModelKitSeries\StoreModelKitSeriesRequest;
use App\Http\Requests\Api\ModelKitSeries\UpdateModelKitSeriesRequest;
use App\Models\ModelKitSeries;
use Illuminate\Support\Str;

class ModelKitSeriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = ModelKitSeries::query()->with('manufacturer');

        if ($search = request('search')) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%$search%")
                    ->orWhere('universe', 'like', "%$search%");
            });
        }

        $series = $query->get();

        return response()->json($series);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreModelKitSeriesRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);

        $series = ModelKitSeries::create($data);
        $series->load('manufacturer');

        return response()->json([
            'message' => 'Tạo series thành công',
            'series' => $series,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $series = ModelKitSeries::with('manufacturer')->find($id);
        if (! $series) {
            return response()->json(['message' => 'Series không tồn tại'], 404);
        }

        return response()->json($series);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateModelKitSeriesRequest $request, string $id)
    {
        $series = ModelKitSeries::find($id);
        if (! $series) {
            return response()->json(['message' => 'Series không tồn tại'], 404);
        }

        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);

        $series->update($data);
        $series->load('manufacturer');

        return response()->json([
            'message' => 'Cập nhật series thành công',
            'series' => $series,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $series = ModelKitSeries::find($id);
        if (! $series) {
            return response()->json(['message' => 'Series không tồn tại'], 404);
        }

        $series->delete();

        return response()->json(['message' => 'Xóa series thành công']);
    }
}
