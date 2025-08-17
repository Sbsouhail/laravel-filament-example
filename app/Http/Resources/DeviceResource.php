<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Device
 */
class DeviceResource extends JsonResource
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
            'identifier' => $this->identifier,
            'fcm_token' => $this->fcm_token,
            'ip_address' => $this->ip_address,
            'platform' => $this->platform,
            'created_at' => $this->created_at,
        ];
    }
}
