<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'current_password' => [
                'bail',
                'required',
                'string',
            ],
            'password' => [
                'bail',
                'required',
                'string',
                'min:8',
                'max:255',
                'confirmed',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required' => 'El campo de contraseña actual es obligatorio.',

            'password.required' => 'El campo de nueva contraseña es obligatorio.',
            'password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'password.max' => 'La nueva contraseña no debe exceder los 255 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'password.regex' => 'La nueva contraseña debe contener al menos una mayúscula, una minúscula y un número.',
        ];
    }

    public function attributes(): array
    {
        return [
            'current_password' => 'contraseña actual',
            'password' => 'nueva contraseña',
            'password_confirmation' => 'confirmación de contraseña',
        ];
    }
}
