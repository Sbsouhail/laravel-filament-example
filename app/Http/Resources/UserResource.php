<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Enums\Gender;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        /** @var Gender */
        $gender = $this->gender;

        return [
            'id' => $this->id,
            'date_of_birth' => new Carbon($this->date_of_birth)->format('Y-m-d'),
            'gender' => [
                'value' => $gender,
                'label' => $gender->getLabel(),
            ],
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'created_at' => $this->created_at,
            'thumbnail_avatar_url' => $this->getFirstMedia('user-avatar')?->getFullUrl('preview') ?? $this->getFirstMedia('user-avatar')?->getFullUrl(),
            'avatar_url' => $this->getFirstMedia('user-avatar')?->getFullUrl(),
            'used_invite_codes_count' => $this->whenCounted('inviteCodes'),
            'redemptions_count' => $this->whenCounted('redemptions'),
        ];
    }
}
