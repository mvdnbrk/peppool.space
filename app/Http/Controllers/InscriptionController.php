<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Data\Ordinals\InscriptionData;
use App\Services\Inscriptions\InscriptionReferenceParser;
use App\Services\OrdinalsService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InscriptionController extends Controller
{
    public function __construct(
        private readonly OrdinalsService $ordinals,
        private readonly InscriptionReferenceParser $referenceParser,
    ) {}

    public function show(string $inscriptionId): View
    {
        try {
            $inscription = $this->ordinals->getInscription($inscriptionId);
        } catch (RequestException $e) {
            throw new NotFoundHttpException('Inscription not found.');
        }

        $references = $this->parseReferences($inscription);

        return view('inscription.show', [
            'inscription' => $inscription,
            'inscriptionId' => $inscriptionId,
            'contentUrl' => '/content/'.$inscriptionId,
            'references' => $references,
        ]);
    }

    /**
     * @return list<string>
     */
    private function parseReferences(InscriptionData $inscription): array
    {
        $contentType = $inscription->effective_content_type;

        if (! str_starts_with($contentType ?? '', 'text/')) {
            return [];
        }

        try {
            $response = $this->ordinals->getContent($inscription->id);

            return $this->referenceParser->parse($contentType, $response->body());
        } catch (RequestException) {
            return [];
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
