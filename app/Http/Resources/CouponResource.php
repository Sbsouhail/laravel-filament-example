<?php

declare(strict_types=1);

namespace App\Http\Resources;

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin object{code: string, used_at: string, created_at: string} */
class CouponResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'code' => $this->code,
            'used_at' => $this->used_at,
            'created_at' => $this->created_at,
        ];
    }
}
