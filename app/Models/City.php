<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    protected $table = 'cities';

    protected $fillable = [
        'name',
        'order',
    ];

    /** @return HasMany<Restaurant,$this> */
    public function restaurants(): HasMany
    {
        return $this->hasMany(Restaurant::class);
    }
}
