<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    protected $primaryKey = 'height';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'height',
        'hash',
        'tx_count',
        'size',
        'difficulty',
        'nonce',
        'version',
        'merkleroot',
        'chainwork',
        'auxpow',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'auxpow' => 'array',
    ];
}
