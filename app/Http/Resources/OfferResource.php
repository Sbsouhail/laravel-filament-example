<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Offer
 */
class OfferResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'banner_url' => $this->getFirstMedia('offer-banner')?->getFullUrl(),
            'restaurant_id' => $this->restaurant_id,
            'restaurant' => $this->whenLoaded('restaurant', new RestaurantResource($this->restaurant)),
        ];
    }
}
