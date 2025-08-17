<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CouponOffer extends Model
{
    protected $table = 'coupon_offers';

    protected $fillable = [
        'title',
        'item_description',
        'validity_period',
        'terms',
        'button_title',
    ];

    /** @return HasMany<Restaurant,$this> */
    public function restaurants(): HasMany
    {
        return $this->hasMany(Restaurant::class);
    }
}
