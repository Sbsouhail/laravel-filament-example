<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PlatformType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Device extends Model
{
    protected $table = 'devices';

    protected $fillable = [
        'user_id',
        'identifier',
        'fcm_token',
        'ip_address',
        'platform',
    ];

    protected $casts = [
        'platform' => PlatformType::class,
    ];

    /** @return BelongsTo <User,$this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
