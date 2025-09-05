<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'subject' => $this->subject,
            'body' => $this->body,
            'status' => $this->status,
            'category' => $this->category,
            'note' => $this->note,
            'ai_explanation' => $this->ai_explanation,
            'ai_confidence' => $this->ai_confidence,
            'manually_overridden' => $this->manually_overridden,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
