<?php

declare(strict_types=1);

namespace App\Http\Requests\Device;

use App\Enums\PlatformType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class RegisterDeviceRequest extends FormRequest
{
    /** Determine if the user is authorized to make this request. */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'platform' => ['required', new Enum(PlatformType::class)],
            'identifier' => 'required',
            'fcm_token' => 'nullable',
            // 'ip_address' => 'nullable',
        ];
    }
}
