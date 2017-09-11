<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class Bar extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'content' => $this->content,
            'publish_date' => $this->publish_date,
            'author_id' => $this->author_id,
            'rate' => $this->rate,
            'score' => $this->score,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
