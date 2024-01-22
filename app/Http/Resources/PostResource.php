<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'no' => $this->no,
            'division' => $this->division,
            'content' => substr($this->content, 0, 50) . '...',
            'inspection_date' => $this->inspection_date,
            
            'history_id' => $this->when(isset($this->history_id), $this->history_id)
        ];
    }
}
