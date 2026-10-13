<?php

namespace App\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SystemActionRequest extends FormRequest
{
    public const ACTIONS = ['clear-cache', 'storage-link', 'migrate', 'seed'];

    /**
     * The secret key from .env must match.
     */
    public function authorize(): bool
    {
        return system_key_valid($this->input('key'));
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'key' => ['required', 'string'],
            'action' => ['required', Rule::in(self::ACTIONS)],
        ];
    }

    /**
     * No session on these routes, so validation errors cannot be flashed back.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator): void
    {
        abort(422, $validator->errors()->first());
    }

    protected function failedAuthorization(): void
    {
        abort(404);
    }
}
