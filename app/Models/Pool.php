<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pool extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'link',
        'addresses',
        'regexes',
        'slug',
        'unique_id',
    ];

    protected function casts(): array
    {
        return [
            'addresses' => 'array',
            'regexes' => 'array',
        ];
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(Block::class);
    }

    public function stats(): HasMany
    {
        return $this->hasMany(PoolStat::class);
    }
}
