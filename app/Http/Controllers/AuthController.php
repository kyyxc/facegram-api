<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required',
            'bio' => 'required|max:100',
            'username' => 'required|min:3|unique:users,username|regex:/^[a-zA-Z0-9._]+$/',
            'password' => 'required|min:3',
            'is_private' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'invalid field',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::create($request->only(['full_name', 'bio', 'username', 'password', 'is_private']));

        if (Auth::attempt($request->only(['username', 'password']))) {
            $token = $user->createToken('SANCTUM')->plainTextToken;
            return response()->json([
                'message' => 'Register success',
                'token' => $token,
                'user' => new UserResource($user)
            ], 201);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'invalid field',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::firstWhere('username', $request->username);

        if (!Auth::attempt($request->only(['username', 'password']))) {
            return response()->json([
                'message' => 'Wrong username or password'
            ], 401);
        }

        $token = $user->createToken('SANCTUM')->plainTextToken;
        return response()->json([
            'message' => 'Login succcess',
            'token' => $token,
            'user' => new UserResource($user)
        ], 200);
    }

    public function logout(Request $request)
    {
        if ($request->user()->tokens()->delete()) {
            return response()->json([
                'message' => 'Logout Success'
            ], 201);
        }
    }
}
