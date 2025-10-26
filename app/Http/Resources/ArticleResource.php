<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'source_id' => $this->source->name ?? 'Unknown',
            'source_article' => $this->source_article_id,
            'title' => $this->title,
            'description' => $this->description,
            'url' => $this->url,
            'published_at' => $this->published_at->format('Y-m-d H:i:s'),
            'author_name' => $this->author_name,
            'category' => $this->category ?? 'Unknown',
            'created_at' => $this->created_at->format('Y-m-d H:i:s')
        ];
    }
}
