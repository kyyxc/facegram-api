<?php

namespace App\Http\Controllers;

use App\Http\Resources\DetailUserResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $following = $user->getFollowing;

        $users = User::all()->filter(function ($item) use ($following, $user) {
            return !$following->contains('id', $item->id) && $item->id != $user->id;
        });

        return response()->json([
            'users' => UserResource::collection($users)
        ]);
    }

    public function detail(Request $request, $username)
    {
        $user = User::firstWhere('username', $username);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'data' => new DetailUserResource($user)
        ]);
    }
}
