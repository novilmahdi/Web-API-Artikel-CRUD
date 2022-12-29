<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostDetailReseource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return[
            'id' => $this->id,
            'titte' => $this->title,
            'image' => $this->image,
            'news_content' => $this->news_content,
            'created_at' => date_format($this->created_at, "Y/m/d H:i:s"),
            'Author' => $this->author,
            'writer' => $this->whenLoaded('writer'), // "whenLoaded" berguna pada saat di controller hanya bisa digunakan bagi yang memanggil relasi menggunakan "with"
            'comments' => $this->whenLoaded('comments', function () {
              return collect($this->comments)->each(function ($comment){
                $comment->commentator;
                return $comment;
              });
            }),
            'comment_total' => $this->whenLoaded('comments', function () {
                return count($this->comments);
            })
        ];
    }
}
