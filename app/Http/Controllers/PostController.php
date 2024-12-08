<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Attacments;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->mergeIfMissing([
            'size' => $request->input('size', 10),
            'page' => $request->input('page', 0)
        ]);

        $validator = Validator::make($request->all(), [
            'size' => 'integer|min:1',
            'page' => 'integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid Fiels',
                'errors' => $validator->errors()
            ], 422);
        }

        $total = $request->page * $request->size;
        $offset =  $request->page == 0 ? $total : $total + 3;

        $followingIds = $request->user()->getFollowing()->get()->pluck('id');


        $posts = Post::whereIn('user_id', $followingIds->push($request->user()->id))
            ->with(['attachments', 'user'])
            ->latest()
            ->limit($request->size)
            ->offset($offset)
            ->get();


        // $posts = Post::with(['attachments', 'user'])->limit($request->size)->offset($offset)->latest()->get();

        return response()->json([
            'page' => $request->page,
            'size' => $request->size,
            'posts' => PostResource::collection($posts)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'caption' => 'required',
            'attachments.*' => 'required|mimes:png,jpg,webp'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid field',
                'errors' => $validator->errors()
            ], 400);
        }

        $post = Post::create([
            'caption' => $request->caption,
            'user_id' => $request->user()->id
        ]);
        foreach ($request->attachments as $attachment) {
            $file = $attachment->store('posts');
            $attacments = Attacments::create([
                'storage_path' => $file,
                'post_id' => $post->id,
            ]);
        }

        if ($post && $attacments) {
            return response()->json([
                'message' => 'Create Post success'
            ], 201);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id, Request $request)
    {
        $post = Post::firstWhere('id', $id);
        if (!$post) {
            return response()->json([
                'message' => 'Post not found'
            ], 404);
        }

        if ($request->user()->id != $post->id) {
            return response()->json([
                'message' => 'Forbidden Access'
            ], 403);
        }

        if ($post->delete()) {
            return response()->json([], 204);
        }
    }
}
