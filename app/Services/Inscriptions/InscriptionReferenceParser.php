<?php

declare(strict_types=1);

namespace App\Services\Inscriptions;

class InscriptionReferenceParser
{
    private const INSCRIPTION_ID_PATTERN = '[0-9a-fA-F]{64}i\d+';

    /**
     * Extract unique inscription IDs referenced via /content/<id> in the given content.
     *
     * @return list<string>
     */
    public function parse(?string $contentType, ?string $content): array
    {
        if ($content === null || $content === '' || ! $this->isTextContent($contentType)) {
            return [];
        }

        $pattern = '/\/content\/('.self::INSCRIPTION_ID_PATTERN.')/';

        if (! preg_match_all($pattern, $content, $matches)) {
            return [];
        }

        return array_values(array_unique($matches[1]));
    }

    private function isTextContent(?string $contentType): bool
    {
        if ($contentType === null) {
            return false;
        }

        $type = strtolower(explode(';', $contentType)[0]);

        return str_starts_with(trim($type), 'text/');
    }
}
