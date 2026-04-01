<?php

declare(strict_types=1);

namespace App\Data\Ordinals;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\CamelCaseMapper;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

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
        'model/gltf+json' => '3D Model',
    ];

    public function __construct(
        public string $id,
        public int $number,
        public ?string $address,
        public int $child_count,
        public array $children,
        public ?string $content_type,
        public ?string $effective_content_type,
        public ?int $content_length,
        public ?string $delegate,
        public int $fee,
        public int $height,
        public ?int $value,
        public int $parent_count,
        public array $parents,
        public ?array $properties,
        public string $satpoint,
        public int $timestamp,
        public ?string $next,
        public ?string $previous,
    ) {}

    public function hasContent(): bool
    {
        return $this->content_length !== null;
    }

    public function contentTypeForHumans(): string
    {
        $baseType = Str::before($this->effective_content_type, ';');

        return self::CONTENT_TYPE_LABELS[$baseType] ?? Str::upper(Str::afterLast($baseType, '/'));
    }

    public function hasTitle(): bool
    {
        return ! empty($this->properties['title']);
    }

    public function hasTraits(): bool
    {
        return ! empty($this->properties['traits']) && is_array($this->properties['traits']);
    }

    public function isDelegate(): bool
    {
        return ! empty($this->delegate);
    }

    public function hasParents(): bool
    {
        return $this->parent_count > 0 && ! empty($this->parents);
    }

    public function hasChildren(): bool
    {
        return $this->child_count > 0 && ! empty($this->children);
    }

    public function getTitle(): ?string
    {
        return $this->properties['title'] ?? null;
    }

    public function getTraits(): Collection
    {
        return collect($this->properties['traits'] ?? []);
    }

    public function getParents(): Collection
    {
        return collect($this->parents);
    }

    public function getChildren(): Collection
    {
        return collect($this->children);
    }

    public function getChildCount(): int
    {
        return $this->child_count;
    }
}
