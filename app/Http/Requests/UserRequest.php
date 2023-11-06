<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $method = $this->method();
            if($method == "PATCH") {
                return ['password'=>'required'];
            }

            return [
                'firstName'=> 'required',
                'lastName'=> 'required',
                'email'=> ['required', 'email', Rule::unique('users')->ignore($this->id)],
                'cpf'=> ['required', 'digits:11', Rule::unique('users')->ignore($this->id)],
                'phone'=> 'required',
                'password'=> Rule::requiredIf($method == "POST"),
                'type'=> Rule::requiredIf($method == "PUT")
            ];

    }

    public function messages(): array
    {
        return [
            'required'=> 'O campo :attribute é o obrigatório.',
            'email.unique'=> 'Email já cadastrado no sistema.',
            'cpf.unique'=> 'CPF já cadastrado no sistema',
            'cpf.digits'=> 'CPF digitado incorretamente'
        ];
    }
}
