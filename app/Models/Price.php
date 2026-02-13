<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'currency',
        'price',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'float',
            'created_at' => 'datetime',
        ];
    }
}
