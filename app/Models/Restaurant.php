<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\OfferType;
use App\Enums\ReserveAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Restaurant extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $table = 'restaurants';

    protected $fillable = [
        'city_id',
        'venue_id',
        'cuisine_id',
        'name',
        'description',
        'location',
        'instagram_url',
        'menu_url',
        'offer',
        'menu_from_file',
        'is_active',
        'coupon_per_user',
        'website_url',
        'reserve_action',
        'phone',
        'lat',
        'lng',
        'mapLocation',
        'open_days',
        'open_time',
        'close_time',
        'exceptions',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'offer' => OfferType::class,
            'reserve_action' => ReserveAction::class,
            'open_time' => 'datetime',
            'close_time' => 'datetime',
        ];
    }

    /** @return BelongsTo<City, $this> */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /** @return BelongsTo<Venue, $this> */
    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    /** @return BelongsTo<Cuisine, $this> */
    public function cuisine(): BelongsTo
    {
        return $this->belongsTo(Cuisine::class);
    }

    /** @return HasMany<Offer,$this> */
    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    /** @return BelongsTo<CouponOffer, $this> */
    public function coupon_offer(): BelongsTo
    {
        return $this->belongsTo(CouponOffer::class);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('preview')
            ->fit(Fit::Contain, 300, 300);
    }

    /** @return HasMany<Redemption,$this> */
    public function redemptions(): HasMany
    {
        return $this->hasMany(Redemption::class);
    }

    protected $appends = [
        'mapLocation',
    ];

    /** @return array<string, float> */
    public function getMapLocationAttribute(): array
    {
        return [
            'lat' => (float) $this->lat,
            'lng' => (float) $this->lng,
        ];
    }

    /** @param array<string, float>|null $location */
    public function setMapLocationAttribute(?array $location): void
    {
        if (is_array($location)) {
            $this->attributes['lat'] = $location['lat'];
            $this->attributes['lng'] = $location['lng'];
            unset($this->attributes['mapLocation']);
        }
    }

    /** @return array<string, string> */
    public static function getLatLngAttributes(): array
    {
        return [
            'lat' => 'lat',
            'lng' => 'lng',
        ];
    }

    public static function getComputedLocation(): string
    {
        return 'mapLocation';
    }
}
