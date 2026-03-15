<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\OrdinalsService;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Throwable;

class InscriptionController extends Controller
{
    public function __construct(
        private readonly OrdinalsService $ordinals,
    ) {}

    public function show(string $inscriptionId): View
    {
        try {
            $inscription = $this->ordinals->getInscription($inscriptionId);

            return view('inscription.show', [
                'inscription' => $inscription,
                'inscriptionId' => $inscriptionId,
                'contentUrl' => '/content/' . $inscriptionId,
            ]);
        } catch (Throwable $e) {
            return view('inscription.show', [
                'error' => config('app.debug') ? $e->getMessage() : 'Inscription not found.',
                'inscriptionId' => $inscriptionId,
            ]);
        }
    }

    public function content(string $inscriptionId): Response
    {
        $response = $this->ordinals->getContent($inscriptionId);

        return response($response->body(), $response->status())
            ->header('Content-Type', $response->header('Content-Type'))
            ->header('Cache-Control', 'public, max-age=31536000, immutable');
    }
}
