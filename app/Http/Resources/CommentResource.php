<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (string)$this->id,
            'comment' => $this->comment,
            'user' => [
                'user_id' => $this->user->id,
                'firstname' => $this->user->firstname,
                'lastname'=> $this->user->lastname,
            ],

        ];
    }
}
