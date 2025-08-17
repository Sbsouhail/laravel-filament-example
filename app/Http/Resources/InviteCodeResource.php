<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\InviteCode;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin InviteCode
 */
class InviteCodeResource extends JsonResource
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
            'user_id' => $this->user_id,
            'code' => $this->code,
            'used_by_id' => $this->used_by_id,
            'used_at' => $this->used_at,
            'created_at' => $this->created_at,
        ];
    }
}
