<?php

namespace App\Http\Resources;

use App\Models\Following;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetailUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $follow = Following::where('follower_id', $request->user()->id)->where('following_id', $this->id)->first();
        $follow_status = '';
        if(!$follow){
            $follow_status = 'not-following';
        } else if($follow->is_accepted){
            $follow_status = 'following';
        } else {
            $follow_status = 'requested';
        }

        $posts = $this->posts->load('attachments');

        return [
            'id' => $this->id,
            'user_id' => $request->user()->id,
            'full_name' => $this->full_name,
            'username' => $this->username,
            'bio' => $this->bio,
            'is_private' => $this->is_private,
            'created_at' => $this->created_at,
            'is_your_account' => $request->user()->id == $this->id ? true : false,
            'following_status' => $follow_status,
            'posts_count' => $this->posts->count(),
            'followers_count' => $this->followers->count(),
            'following_count' => $this->following->where('is_accepted', true)->count(),
            'posts' => $posts->mapInto(PostResource::class)
        ];
    }
}
