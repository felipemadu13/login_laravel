<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AuthRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $passwordUpdate = $this->is('api/forgot-password/update');

        return [
            'email' => 'required|email',
            'token' => Rule::requiredIf($passwordUpdate),
            'password' => [Rule::requiredIf($passwordUpdate), 'confirmed'],
        ];


    }

    public function messages(): array
    {
        return [
            'required'=> 'O campo :attribute é o obrigatório.',
            'email.email'=> 'O campo e-mail deve ser um endereço valido',
            'password.confirmed' => 'A nova senha e a de confirmação devem ser iguais'
        ];
    }
}
