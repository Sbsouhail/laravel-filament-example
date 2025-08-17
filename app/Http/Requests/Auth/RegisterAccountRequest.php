<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Enums\Gender;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class RegisterAccountRequest extends FormRequest
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
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required|phone|unique:users',
            'email' => 'required|email|unique:users',
            'gender' => ['required', new Enum(Gender::class)],
            'date_of_birth' => 'required|date_format:Y-m-d',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required',
            'device_name' => 'required',
            'invite_code' => 'nullable',
            'avatar' => ['nullable', 'image', 'max:2048'],
        ];
    }
}
