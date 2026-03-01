<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Node extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip',
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
            'is_online' => 'boolean',
            'last_seen_at' => 'datetime',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }
}
