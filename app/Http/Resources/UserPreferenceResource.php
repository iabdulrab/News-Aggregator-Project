<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserPreferenceResource extends JsonResource
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
            'user_id' => $this->user_id,
            'user_name' => $this->user->name,
            'preferences' => [
                'sources' => $this->preferences['sources'],
                'categories' => $this->preferences['categories'],
                'authors' => $this->preferences['authors']
            ],
            'created_at' => $this->created_at->format('Y-m-d H:i:s')
        ];
    }
}
