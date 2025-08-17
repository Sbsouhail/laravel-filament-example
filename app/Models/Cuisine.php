<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cuisine extends Model
{
    protected $table = 'cuisines';

    protected $fillable = [
        'name',
    ];

    /** @return HasMany<Restaurant,$this> */
    public function restaurants(): HasMany
    {
        return $this->hasMany(Restaurant::class);
    }
}
