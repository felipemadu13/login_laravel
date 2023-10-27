<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'firstName'=> 'required',
            'lastName'=> 'required',
            'email'=> ['required', 'email', Rule::unique('users')->ignore($this->id)],
            'cpf'=> ['required', 'digits:11', Rule::unique('users')->ignore($this->id)],
            'phone'=> 'required',
            'password'=> 'required'
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
