<?php

namespace App\Http\Resources\Reply;

use Illuminate\Http\Resources\Json\JsonResource;

class ReplyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'reply_id'=>$this->id,
            'body'=>$this->body,
            'author'=>$this->user->name,
            'user_id'=>$this->user_id,
            'likes'=>$this->likes->count(),
            'liked'=>!! $this->likes->where('user_id',auth()->id())->count(),
            'commented_at'=>$this->created_at->format('d M H:i'),
        ];
    }
}
