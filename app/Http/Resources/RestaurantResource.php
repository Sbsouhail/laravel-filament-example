<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Enums\Day;
use App\Enums\ReserveAction;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Restaurant
 */
class RestaurantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var ReserveAction|null */
        $reserve_action = $this->reserve_action;

        /** @var string[] */
        $open_days = $this->open_days ? explode(',', $this->open_days) : [];

        /** @var string[] */
        $exceptions = $this->exceptions
            ? array_values(array_filter(preg_split('/\r\n|\r|\n/', $this->exceptions) ?: []))
            : [];

        return [
            'id' => $this->id,
            'name' => $this->name,
            'city_id' => $this->city_id,
            'city' => $this->whenLoaded('city',  new CityResource($this->city)),
            'cuisine_id' => $this->cuisine_id,
            'cuisine' => $this->whenLoaded('cuisine', new CuisineResource($this->cuisine)),
            'venue_id' => $this->venue_id,
            'venue' => $this->whenLoaded('venue', new VenueResource($this->venue)),
            'instagram_url' => $this->instagram_url,
            'website_url' => $this->website_url,
            'reserve_action' => $reserve_action ? [
                'value' => $reserve_action,
                'label' => $reserve_action->getLabel(),
            ] : null,
            'phone' => $this->phone,
            'coupon_offer' => $this->whenLoaded('coupon_offer', new CouponOfferResource($this->coupon_offer)),
            'menu_url' => $this->menu_from_file
                ? $this->getFirstMedia('restaurant-menu')?->getFullUrl()
                : $this->menu_url,
            'coupon_per_user' => $this->coupon_per_user,
            'description' => $this->description,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'location' => $this->location,
            'thumbnail_url' => $this->getFirstMedia('restaurant-images')?->getFullUrl('preview')
                ?? $this->getFirstMedia('restaurant-images')?->getFullUrl(),
            /** @var string[] */
            'images' => $this->getMedia('restaurant-images')->map(fn ($media) => $media->getFullUrl()),
            /**
             * @var array<int, array{value: string, label: string}>
             */
            'open_days' => array_map(
                fn (string $day) => ['value' => $day, 'label' => Day::from($day)->getLabel()],
                $open_days
            ),
            'open_time' => $this->open_time,
            'close_time' => $this->close_time,
            /** @var string[] */
            'exceptions' => $exceptions,
        ];
    }
}
