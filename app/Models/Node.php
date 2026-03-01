<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Node extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip',
        'sources',
        'port',
        'version',
        'subversion',
        'continent',
        'continent_code',
        'country',
        'country_code',
        'region',
        'region_code',
        'city',
        'latitude',
        'longitude',
        'isp',
        'is_online',
        'last_seen_at',
    ];

    protected function casts(): array
    {
        return [
            'sources' => 'array',
            'is_online' => 'boolean',
            'last_seen_at' => 'datetime',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    protected function clientVersion(): Attribute
    {
        return Attribute::make(
            get: fn () => str_replace(':', ' ', ucfirst(trim(explode('(', $this->subversion ?? 'unknown')[0], '/')))
        );
    }

    protected function userComment(): Attribute
    {
        return Attribute::make(
            get: fn () => preg_match('/\((.*)\)/', $this->subversion ?? '', $matches) ? $matches[1] : null
        );
    }
}
