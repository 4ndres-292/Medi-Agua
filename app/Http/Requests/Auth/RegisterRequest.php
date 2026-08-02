<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'username' => [
                'bail',
                'required',
                'string',
                'min:2',
                'max:255',
                'regex:/^[\p{L}\s]+$/u',
            ],
            'lastname' => [
                'bail',
                'required',
                'string',
                'min:2',
                'max:255',
                'regex:/^[\p{L}\s]+$/u',
            ],
            'email' => [
                'bail',
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
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
            'username.required' => 'El campo de nombre es obligatorio.',
            'username.min' => 'El nombre debe tener al menos 2 caracteres.',
            'username.max' => 'El nombre no debe exceder los 255 caracteres.',
            'username.regex' => 'El nombre solo debe contener letras y espacios.',

            'lastname.required' => 'El campo de apellido es obligatorio.',
            'lastname.min' => 'El apellido debe tener al menos 2 caracteres.',
            'lastname.max' => 'El apellido no debe exceder los 255 caracteres.',
            'lastname.regex' => 'El apellido solo debe contener letras y espacios.',

            'email.required' => 'El campo de correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección de correo válida.',
            'email.max' => 'El correo electrónico no debe exceder los 255 caracteres.',
            'email.unique' => 'El correo electrónico ya está en uso.',

            'password.required' => 'El campo de contraseña es obligatorio.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.max' => 'La contraseña no debe exceder los 255 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'password.regex' => 'La contraseña debe contener al menos una mayúscula, una minúscula y un número.',
        ];
    }

    public function attributes(): array
    {
        return [
            'username' => 'nombre',
            'lastname' => 'apellido',
            'email' => 'correo electrónico',
            'password' => 'contraseña',
            'password_confirmation' => 'confirmación de contraseña',
        ];
    }
}
