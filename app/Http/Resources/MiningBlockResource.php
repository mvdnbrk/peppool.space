<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MiningBlockResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->hash,
            'height' => $this->height,
            'timestamp' => $this->created_at->timestamp,
            'tx_count' => $this->tx_count,
            'size' => $this->size,
            'pool' => $this->pool ? [
                'name' => $this->pool->name,
                'slug' => $this->pool->slug,
            ] : null,
        ];
    }
}
