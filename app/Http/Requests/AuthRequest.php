<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'=> ['required', 'email']
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'=> 'E-mail obrigatório',
            'email.email'=> 'O campo e-mail deve ser um endereço valido'
        ];
    }
}
