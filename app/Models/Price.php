<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'currency',
        'price',
        'created_at',
    ];

    protected $casts = [
        'price' => 'float',
        'created_at' => 'datetime',
    ];
}
