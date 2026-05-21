<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\StoreUserRequest;
use App\Http\Requests\Api\User\UpdateUserRequest;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $search = request()->query('search');
        $users = User::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            })
            ->paginate(10);

        return response()->json($users);
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        $user = User::create($data);

        return response()->json([
            'message' => 'Người dùng đã được tạo thành công',
            'user' => $user,
        ], 201);
    }

    public function show(string $id)
    {
        $user = User::find($id);

        if (! $user) {
            return response()->json([
                'message' => 'Người dùng không tồn tại',
            ], 404);
        }

        return response()->json($user);
    }

    public function update(UpdateUserRequest $request, string $id)
    {
        $user = User::find($id);

        if (! $user) {
            return response()->json([
                'message' => 'Người dùng không tồn tại',
            ], 404);
        }

        $data = $request->validated();
        $user->update($data);

        return response()->json([
            'message' => 'Người dùng đã được cập nhật thành công',
            'user' => $user,
        ], 200);
    }

    public function destroy(string $id)
    {
        $user = User::find($id);

        if (! $user) {
            return response()->json([
                'message' => 'Người dùng không tồn tại',
            ], 404);
        }

        $user->delete();

        return response()->json([
            'message' => 'Người dùng đã được xóa thành công',
        ], 200);
    }
}
