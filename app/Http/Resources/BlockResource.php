<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BlockResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->hash,
            'height' => $this->height,
            'version' => $this->version,
            'timestamp' => optional($this->created_at)->timestamp,
            'tx_count' => $this->tx_count,
            'size' => $this->size,
            'difficulty' => $this->difficulty,
            'nonce' => $this->nonce,
            'merkle_root' => $this->merkleroot,
        ];
    }
}
