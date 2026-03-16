<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Block;
use App\Services\OrdinalsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\RequestException;
use Illuminate\Queue\InteractsWithQueue;

class FetchBlockInscriptionCount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public int $tries = 3;

    public int $backoff = 10;

    public function __construct(
        public readonly int $height,
    ) {}

    public function handle(OrdinalsService $ordinals): void
    {
        try {
            $count = $ordinals->getBlockInscriptionCount($this->height);
        } catch (RequestException $e) {
            if ($e->response->status() === 404) {
                return;
            }

            throw $e;
        }

        Block::where('height', $this->height)
            ->update(['inscription_count' => $count]);
    }
}
