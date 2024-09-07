<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AuthRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $mappings = config('providers');
        $keys = array_keys($mappings);
        $prefixes = implode('|', $keys);
        $regexPattern = "/^({$prefixes})_\w+$/";

        return [
            'login' => [
                'required',
                'string',
                'min:3',
                'max:20',
                "regex:{$regexPattern}",
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[-]/',
            ],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        if ($this->expectsJson()) {
            throw new HttpResponseException(
                response()->json(['status' => 'failure'], 422)
            );
        }

        parent::failedValidation($validator);
    }
}
