<?php

namespace App\Http\Controllers;

use App\Http\Resources\FollowerResource;
use App\Http\Resources\FollowingResource;
use App\Models\Following;
use App\Models\User;
use Illuminate\Http\Request;

class FollowingController extends Controller
{
    public function follow(Request $request, $user)
    {
        $user = User::firstWhere('username', $user);
        if (!$user) {
            return response()->json([
                'message' => 'Username not found',
            ], 404);
        }

        if ($user->username == $request->user()->username) {
            return response()->json([
                'message' => 'Your are not allowed to follow yourself',
            ], 422);
        }

        $is_accepted = true;
        if ($user->is_private) {
            $is_accepted = false;
        }

        $alreadyFollow = Following::where('follower_id', $request->user()->id)
            ->where('following_id', $user->id)->first();

        if ($alreadyFollow) {
            return response()->json([
                'message' => 'Your are already followerd',
                'status' => $alreadyFollow->is_accepted == 1 ? 'following' : 'requested',
            ], 422);
        }

        $follow = Following::create([
            'follower_id' => $request->user()->id,
            'following_id' => $user->id,
            'is_accepted' => $is_accepted,
        ]);

        return response()->json([
            'message' => 'Follow success',
            'status' => $is_accepted ? 'following' : 'requested',
        ]);
    }

    public function unfollow(Request $request, $user)
    {
        $user = User::firstWhere('username', $user);

        if (!$user) {
            return response()->json([
                'message' => 'Username not found',
            ], 404);
        }

        $follow = Following::where('follower_id', $request->user()->id)->where('following_id', $user->id)->first();

        if (!$follow) {
            return response()->json([
                'message' => 'You are not following the user',
            ], 422);
        }

        if ($follow->delete()) {
            return response()->json([], 204);
        }
    }

    public function getFollowing(Request $request, $username)
    {
        $username = User::firstWhere('username', $username);
        if (!$username) {
            return response()->json([
                'status' => 'User Not Found',
            ], 404);
        }
        $following = $username->following;
        return response()->json([
            "following" => FollowingResource::collection($following),
        ], 200);
    }

    public function accept(Request $request, $user)
    {
        $user = User::firstWhere('username', $user);

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        $follow = Following::where('follower_id', $user->id)
            ->where('following_id', $request->user()->id)
            ->first();

        if (!$follow) {
            return response()->json([
                'message' => 'The user is not following you',
            ], 422);
        }

        if ($follow->is_accepted) {
            return response()->json([
                'message' => 'Follow request is already accepted',
            ], 422);
        }

        $follow->update([
            'is_accepted' => true,
        ]);

        return response()->json([
            'message' => 'Follow request accepted',
        ], 200);
    }

    public function getFollowers(Request $request, $username)
    {
        $username = User::firstWhere('username', $username);
        if (!$username) {
            return response()->json([
                'status' => 'User Not Found',
            ], 404);
        }

        $follower = $username->followers;

        return response()->json([
            "followers" => FollowerResource::collection($follower),
        ], 200);
    }
}
