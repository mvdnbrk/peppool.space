<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\InscriptionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inscription extends Model
{
    /** @use HasFactory<InscriptionFactory> */
    use HasFactory;

    public const FLAG_MULTI_PARENT = 1;

    public const FLAG_BITMAP = 2;

    public const FLAG_PRC20 = 4;

    public const FLAG_DELEGATE = 8;

    public const FLAG_HAS_TITLE = 16;

    public const FLAG_HAS_TRAITS = 32;

    public const FLAG_HAS_CHILDREN = 64;

    public const FLAG_HAS_RECURSION = 128;

    public const FLAG_CONTENT_STORED = 256;

    public const FLAG_ANALYZED = 512;

    public const FLAG_GARBAGE = 1024;

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'id',
        'inscription_id',
        'parent_id',
        'delegate_id',
        'content_encoding',
        'content_type',
        'content_length',
        'content',
        'properties',
        'flags',
        'block',
    ];

    protected function casts(): array
    {
        return [
            'properties' => 'array',
        ];
    }

    public function blockRelation(): BelongsTo
    {
        return $this->belongsTo(Block::class, 'block', 'height');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id', 'inscription_id');
    }

    public function delegate(): BelongsTo
    {
        return $this->belongsTo(self::class, 'delegate_id', 'inscription_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'inscription_id');
    }

    public function delegatedBy(): HasMany
    {
        return $this->hasMany(self::class, 'delegate_id', 'inscription_id');
    }
}
