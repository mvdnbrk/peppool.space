<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\OrdinalsService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InscriptionController extends Controller
{
    public function __construct(
        private readonly OrdinalsService $ordinals,
    ) {}

    public function show(string $inscriptionId): View
    {
        try {
            $inscription = $this->ordinals->getInscription($inscriptionId);
        } catch (RequestException $e) {
            throw new NotFoundHttpException('Inscription not found.');
        }

        return view('inscription.show', [
            'inscription' => $inscription,
            'inscriptionId' => $inscriptionId,
            'contentUrl' => '/content/'.$inscriptionId,
        ]);
    }

    public function content(string $inscriptionId): Response
    {
        $response = $this->ordinals->getContent($inscriptionId);

        return response($response->body(), $response->status())
            ->header('Content-Type', $response->header('Content-Type'))
            ->header('Cache-Control', 'public, max-age=31536000, immutable')
            ->withoutHeader('X-Frame-Options');
    }
}
