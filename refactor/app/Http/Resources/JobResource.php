<?php

namespace DTApi\Http\Resources;

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => $this->user->only(['id', 'name', 'userMeta', 'average']),
            'translator' => $this->translatorJobRel->user->only(['id', 'name', 'average']),
            'language' => $this->language,
            'feedback' => $this->feedback,
            'status' => $this->status,
            'due' => $this->due,
            'immediate' => $this->immediate,
            'usercheck' => Job::checkParticularJob($request->user_id, $this),
        ];
    }
}
