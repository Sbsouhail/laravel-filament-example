<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Enums\Gender;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateProfileRequest extends FormRequest
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
            'first_name' => 'string',
            'last_name' => 'string',
            'phone' => 'phone|unique:users,phone,' . $this->user()?->id,
            'email' => 'email|unique:users,email,' . $this->user()?->id,
            'gender' => [new Enum(Gender::class)],
            'date_of_birth' => 'date_format:Y-m-d',
            'avatar' => ['nullable', 'image', 'max:20480'],
        ];
    }
}
