<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Redemption extends Model
{
    protected $table = 'redemptions';

    protected $fillable = [
        'user_id',
        'restaurant_id',
        'used_at',
        'code',
    ];

    protected $casts = [
        'used_at' => 'datetime',
    ];

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Restaurant, $this> */
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function markAsUsed(): void
    {
        $this->used_at = now();
        $this->save();
    }
}
