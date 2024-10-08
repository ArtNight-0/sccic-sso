<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Models\User;

class UserController extends Controller
{
    public function getUser()
    {
        $users = User::all();

        if ($users->isEmpty()) {
            return response()->json([
                'message' => 'Data user tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'message' => 'Data user berhasil ditemukan.',
            // 'data' => $users
            'data' => UserResource::collection($users)
        ], 200);
    }

    public function getUserId($id)
    {
        $users = User::find($id);

        if (!$users) {
            return response()->json([
                'message' => 'Data user tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Data user berhasil ditemukan',
            // 'data' => $users
            'data' => new UserResource($users)
        ], 200);
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);

        $users = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->json([
            'message' => 'Data user berhasil disimpan.',
            'data' => new UserResource($users)
        ], 201);
    }

    public function updateUser(Request $request, $id)
    {
        $users = User::find($id);

        if (!$users) {
            return response()->json([
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            // 'password' => 'required|string|min:8'
        ]);

        $users->name = $request->name;
        $users->email = $request->email;

        if ($request->filled('password')) {
            $users->password = bcrypt($request->password);
        }

        $users->save();

        return response()->json([
            'message' => 'Data user berhasil diubah.',
            'data' => new UserResource($users)
        ]);
    }

    public function destroyUser($id)
    {
        $users = User::find($id);

        if (!$users) {
            return response()->json([
                'message' => 'Data user tidak ditemukan.'
            ], 404);
        }

        $users->delete();

        return response()->json([
            'message' => 'Data user berhasil dihapus.',
            'data' => new UserResource($user)
        ], 200);
    }
}
