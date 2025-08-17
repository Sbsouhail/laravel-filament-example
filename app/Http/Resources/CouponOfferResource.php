<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\CouponOffer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin CouponOffer
 */
class CouponOfferResource extends JsonResource
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
            'item_description' => $this->item_description,
            'validity_period' => $this->validity_period,
            'terms' => $this->terms,
            'button_title' => $this->button_title,
        ];
    }
}
