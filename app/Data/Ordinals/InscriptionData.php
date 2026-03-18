<?php

declare(strict_types=1);

namespace App\Data\Ordinals;

use Illuminate\Support\Str;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\CamelCaseMapper;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Optional;

#[MapInputName(CamelCaseMapper::class)]
#[MapOutputName(SnakeCaseMapper::class)]
final class InscriptionData extends Data
{
    private const CONTENT_TYPE_LABELS = [
        'image/png' => 'PNG Image',
        'image/jpeg' => 'JPEG Image',
        'image/gif' => 'GIF Image',
        'image/webp' => 'WebP Image',
        'image/svg+xml' => 'SVG Image',
        'image/avif' => 'AVIF Image',
        'text/plain' => 'Plain Text',
        'text/html' => 'HTML',
        'text/css' => 'CSS',
        'text/javascript' => 'JavaScript',
        'application/json' => 'JSON',
        'application/pdf' => 'PDF',
        'audio/mpeg' => 'MP3 Audio',
        'audio/wav' => 'WAV Audio',
        'audio/ogg' => 'OGG Audio',
        'video/mp4' => 'MP4 Video',
        'video/webm' => 'WebM Video',
        'model/gltf-binary' => '3D Model',
    ];

    public function __construct(
        public string $id,
        public int $number,
        public string $address,
        public string $content_type,
        public int $content_length,
        public int $fee,
        public int $height,
        public int $value,
        public string $satpoint,
        public int $timestamp,
        public string|Optional|null $next,
        public string|Optional|null $previous,
    ) {}

    public function contentTypeForHumans(): string
    {
        $baseType = Str::before($this->content_type, ';');

        return self::CONTENT_TYPE_LABELS[$baseType] ?? Str::upper(Str::afterLast($baseType, '/'));
    }
}
