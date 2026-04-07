<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\WalletUserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class WalletUser extends Authenticatable
{
    /** @use HasFactory<WalletUserFactory> */
    use HasApiTokens, HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'address',
        'version',
    ];
}
